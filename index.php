<?php

$resultados = [];

$arquivo = "resultados.csv";

if (($handle = fopen($arquivo, "r")) !== FALSE) {

    fgetcsv($handle, 1000, ",");

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

        $resultados[] = [
            "concurso" => $data[0],
            "dezenas" => $data[1]
        ];
    }

    fclose($handle);
}

$resultados = array_slice($resultados, -10);

// ==========================
// FREQUÊNCIA
// ==========================

$frequencia = [];

for($i=1; $i<=60; $i++){

    $numero = str_pad($i, 2, "0", STR_PAD_LEFT);

    $frequencia[$numero] = 0;
}

foreach($resultados as $resultado){

    $dezenas = explode(",", $resultado['dezenas']);

    foreach($dezenas as $dezena){

        $dezena = trim($dezena);

        $frequencia[$dezena]++;
    }
}

arsort($frequencia);

$quentes = array_slice($frequencia, 0, 10, true);

$frias = array_slice(array_reverse($frequencia, true), 0, 10, true);

// ==========================
// JOGOS
// ==========================

$mais_sorteadas = array_keys($quentes);

shuffle($mais_sorteadas);

$jogo_quente = array_slice($mais_sorteadas, 0, 6);

sort($jogo_quente);

$menos_sorteadas = array_keys($frias);

shuffle($menos_sorteadas);

$jogo_frio = array_slice($menos_sorteadas, 0, 6);

sort($jogo_frio);

$todas = range(1, 60);

shuffle($todas);

$jogo_normal = array_slice($todas, 0, 6);

sort($jogo_normal);

// ==========================
// COMPARAÇÃO
// ==========================

$comparacao = [];

if(count($resultados) >= 2){

    $ultimo = $resultados[count($resultados)-1];
    $anterior = $resultados[count($resultados)-2];

    $dezenas_ultimo = explode(",", $ultimo['dezenas']);
    $dezenas_anterior = explode(",", $anterior['dezenas']);

    $repetidas = array_intersect($dezenas_ultimo, $dezenas_anterior);

    $comparacao = [
        "ultimo" => $ultimo,
        "anterior" => $anterior,
        "repetidas" => $repetidas
    ];
}

// ==========================
// ÚLTIMOS 5
// ==========================

$ultimos5 = array_slice($resultados, -5);

$frequencia_5 = [];

foreach($ultimos5 as $resultado){

    $dezenas = explode(",", $resultado['dezenas']);

    foreach($dezenas as $dezena){

        $dezena = trim($dezena);

        if(!isset($frequencia_5[$dezena])){

            $frequencia_5[$dezena] = 0;
        }

        $frequencia_5[$dezena]++;
    }
}

$repetidas_5 = [];

foreach($frequencia_5 as $dezena => $total){

    if($total >= 2){

        $repetidas_5[$dezena] = $total;
    }
}

arsort($repetidas_5);

// ==========================
// GRÁFICO
// ==========================

$labels = array_keys($frequencia);
$valores = array_values($frequencia);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Mega-Sena</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:#f2f2f2;
    overflow-x:hidden;
    font-family:Arial, Helvetica, sans-serif;
}

.navbar{
    background:#198754;
    padding:15px;
}

.navbar-brand{
    font-size:18px;
    line-height:1.5;
    white-space:normal;
}

.mega-card{
    border:none;
    border-radius:20px;
    overflow:hidden;
    transition:0.3s;
}

.mega-card:hover{
    transform:translateY(-5px);
}

.card{
    border:none;
    border-radius:20px;
}

.titulo-concurso{
    color:#198754;
    font-weight:bold;
    font-size:28px;
}

h1{
    font-size:42px;
    font-weight:bold;
}

h2{
    font-size:32px;
    font-weight:bold;
}

h3{
    font-size:26px;
    font-weight:bold;
}

h4{
    font-size:22px;
    font-weight:bold;
}

.dezena{
    width:60px;
    height:60px;
    border-radius:50%;
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:22px;
    font-weight:bold;
    margin:5px;
    box-shadow:0 4px 10px rgba(0,0,0,0.2);
    flex-shrink:0;
}

.btn{
    border-radius:12px;
    padding:10px 20px;
    font-weight:bold;
}

footer{
    background:#198754;
    margin-top:50px;
    font-size:14px;
}

#graficoMega{
    width:100% !important;
    max-height:400px;
}

@media(max-width:768px){

    .navbar-brand{
        font-size:14px;
        text-align:center;
    }

    h1{
        font-size:28px;
    }

    h2{
        font-size:24px;
    }

    h3{
        font-size:20px;
    }

    h4{
        font-size:18px;
    }

    .titulo-concurso{
        font-size:22px;
    }

    .dezena{
        width:45px;
        height:45px;
        font-size:17px;
        margin:4px;
    }

    .card-body{
        padding:18px;
    }

    .btn{
        width:100%;
    }

    #graficoMega{
        max-height:280px;
    }

}

@media(max-width:480px){

    .dezena{
        width:40px;
        height:40px;
        font-size:15px;
    }

    h1{
        font-size:24px;
    }

    h2{
        font-size:21px;
    }

}

</style>

</head>
<body>

<nav class="navbar navbar-dark">

    <div class="container">

        <span class="navbar-brand fw-bold text-center w-100">
            🎰 Mega-Sena Painel - Atenção: Isso Aqui é Simulação
        </span>

    </div>

</nav>

<div class="container py-5">

    <h1 class="text-center mb-5">
        Resultados da Mega-Sena
    </h1>

    <div class="row g-4">

        <?php foreach($resultados as $resultado): ?>

        <div class="col-lg-6">

            <div class="card shadow mega-card h-100">

                <div class="card-body">

                    <div class="titulo-concurso mb-4">
                        Concurso #<?php echo $resultado['concurso']; ?>
                    </div>

                    <div class="d-flex flex-wrap justify-content-center">

                        <?php

                        $dezenas = explode(",", $resultado['dezenas']);

                        foreach($dezenas as $dezena):

                        ?>

                        <div class="dezena bg-success">
                            <?php echo trim($dezena); ?>
                        </div>

                        <?php endforeach; ?>

                    </div>

                </div>

            </div>

        </div>

        <?php endforeach; ?>

    </div>

    <!-- QUENTES E FRIAS -->
    <div class="row g-4 mt-4">

        <div class="col-lg-6">

            <div class="card shadow h-100">

                <div class="card-body">

                    <h3 class="text-danger mb-4">
                        🔥 Dezenas Quentes
                    </h3>

                    <div class="d-flex flex-wrap justify-content-center">

                        <?php foreach($quentes as $dezena => $total): ?>

                            <div class="text-center">

                                <div class="dezena bg-danger">
                                    <?php echo $dezena; ?>
                                </div>

                                <small><?php echo $total; ?>x</small>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-lg-6">

            <div class="card shadow h-100">

                <div class="card-body">

                    <h3 class="text-primary mb-4">
                        ❄️ Dezenas Frias
                    </h3>

                    <div class="d-flex flex-wrap justify-content-center">

                        <?php foreach($frias as $dezena => $total): ?>

                            <div class="text-center">

                                <div class="dezena bg-primary">
                                    <?php echo $dezena; ?>
                                </div>

                                <small><?php echo $total; ?>x</small>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

 

    <!-- GRÁFICO -->
    <div class="card shadow mt-5">

        <div class="card-body">

            <h2 class="text-center mb-4">
                📈 Frequência das Dezenas
            </h2>

            <canvas id="graficoMega"></canvas>

        </div>

    </div>

</div>

<footer class="text-white text-center py-3">
    © <?php echo date("Y"); ?> Sistema Mega-Sena
</footer>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const ctx = document.getElementById('graficoMega');

new Chart(ctx, {

    type: 'bar',

    data: {

        labels: <?php echo json_encode($labels); ?>,

        datasets: [{

            label: 'Quantidade Sorteada',

            data: <?php echo json_encode($valores); ?>,

            borderWidth: 1

        }]
    },

    options: {

        responsive: true,

        maintainAspectRatio: false,

        scales: {

            y: {
                beginAtZero: true
            }
        }
    }
});

</script>

</body>
</html>