<?php
$key='AIzaSyBqz5fzVRwkyIVuSj9SH_nAfDKBcYPd2RA';
$url='https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-lite:generateContent?key='.$key;
$data=json_encode(['contents'=>[['parts'=>[['text'=>'Dis bonjour en francais']]]]]);
$ch=curl_init($url);
curl_setopt($ch,CURLOPT_POST,1);
curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
curl_setopt($ch,CURLOPT_HTTPHEADER,['Content-Type: application/json']);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
$r=curl_exec($ch);
echo $r;
