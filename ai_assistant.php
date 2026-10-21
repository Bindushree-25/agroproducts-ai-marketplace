<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['error'=>'POST required']); exit; }
$body=json_decode(file_get_contents('php://input'),true); $prompt=trim((string)($body['prompt']??''));
if ($prompt==='' || mb_strlen($prompt)>700) { http_response_code(422); echo json_encode(['error'=>'Enter a question up to 700 characters.']); exit; }
if (!isset($_SESSION['ai_last_request'])) $_SESSION['ai_last_request']=0;
if (time()-(int)$_SESSION['ai_last_request']<2) { http_response_code(429); echo json_encode(['error'=>'Please wait a moment before asking again.']); exit; }
$_SESSION['ai_last_request']=time(); $products=[];
try { require_once __DIR__.'/db_connect.php'; if(isset($conn)&&!$conn->connect_error){$q=$conn->query('SELECT p_name,category,price,stock_quantity,description FROM products ORDER BY p_name LIMIT 60'); if($q)while($r=$q->fetch_assoc())$products[]=$r; $conn->close();} } catch(Throwable $e) {}
$config=[]; if(is_file(__DIR__.'/config.local.php')) { $loaded=require __DIR__.'/config.local.php'; if(is_array($loaded))$config=$loaded; }
$key=trim((string)($config['ai_api_key']??'')); $catalogue=json_encode($products,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
if($key!=='') {
 $payload=['model'=>(string)($config['ai_model']??'gpt-4o-mini'),'temperature'=>0.3,'messages'=>[
  ['role'=>'system','content'=>'You are Agro Intelligence, a careful assistant inside a farm-to-table marketplace. Use only the supplied catalogue for product availability and prices. Do not invent products, stock, farmer earnings, market forecasts, medical claims, or nutrition claims. If data is missing, say so. Offer concise, practical shopping suggestions or listing-quality feedback. Do not ask for or reveal personal data. Catalogue JSON: '.$catalogue],
  ['role'=>'user','content'=>$prompt]
 ]];
 $context=stream_context_create(['http'=>['method'=>'POST','header'=>"Content-Type: application/json\r\nAuthorization: Bearer ".$key."\r\n",'content'=>json_encode($payload),'timeout'=>18,'ignore_errors'=>true]]);
 $response=@file_get_contents((string)($config['ai_endpoint']??'https://api.openai.com/v1/chat/completions'),false,$context);
 if($response!==false){$decoded=json_decode($response,true);$answer=$decoded['choices'][0]['message']['content']??null;if(is_string($answer)&&$answer!==''){echo json_encode(['answer'=>$answer,'mode'=>'generative']);exit;}}
}
$lower=mb_strtolower($prompt); $matches=[];
foreach($products as $p){$name=mb_strtolower((string)$p['p_name']);$desc=mb_strtolower((string)($p['description']??''));$cat=mb_strtolower((string)$p['category']);$score=0;foreach(preg_split('/\s+/',preg_replace('/[^\pL\pN ]/u',' ',$lower)) as $word){if(mb_strlen($word)<3)continue;if(str_contains($name,$word))$score+=3;if(str_contains($desc,$word)||str_contains($cat,$word))$score++;}if(str_contains($lower,'budget')||str_contains($lower,'cheap')||str_contains($lower,'low cost'))$score+=max(0,5-(float)$p['price']/50);if((int)$p['stock_quantity']<=0)$score-=20;if($score>0)$matches[]=['p'=>$p,'score'=>$score];}
usort($matches,fn($a,$b)=>$b['score']<=>$a['score']);$matches=array_slice($matches,0,5);
if($matches){$lines=[];foreach($matches as $m){$p=$m['p'];$lines[]=$p['p_name'].' ('.$p['category'].') — ₹'.number_format((float)$p['price'],2).', stock: '.(int)$p['stock_quantity'].'.';}$answer="Here are catalogue matches based on your wording:\n• ".implode("\n• ",$lines)."\n\nCheck the marketplace for the latest details before ordering. This local fallback ranks text matches; it is not generative AI.";}else{$answer="I can help with product discovery, a budget-oriented shortlist, or product listing quality. The current catalogue does not contain enough matching text to answer that specifically. Try a product name or category. This is the local fallback; configure your own API key for generative AI responses.";}
echo json_encode(['answer'=>$answer,'mode'=>'local']);
