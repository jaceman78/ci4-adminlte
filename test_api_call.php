<?php
// Simular uma chamada POST para getDataTable
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/ci4-adminlte/public/sessoes-exame/getDataTable');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'draw' => 1,
    'start' => 0,
    'length' => 10,
    'search' => ['value' => '']
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-Requested-With: XMLHttpRequest'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n\n";
echo "Response:\n";
$json = json_decode($response, true);
print_r($json);

if (isset($json['data']) && !empty($json['data'])) {
    echo "\n\nPrimeira linha de dados:\n";
    print_r($json['data'][0]);
}
