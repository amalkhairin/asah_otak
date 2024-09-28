<?php
session_start();
require_once 'db_connection.php';

function getRandomWord($pdo) {
    $stmt = $pdo->query("SELECT * FROM master_kata ORDER BY RAND() LIMIT 1");
    return $stmt->fetch();
}

function calculateScore($userAnswer, $correctAnswer) {
    $score = 0;
    for ($i = 0; $i < strlen($correctAnswer); $i++) {
        if ($i == 2 || $i == 6) {
            continue;
        }
        if ($userAnswer[$i] == $correctAnswer[$i]) {
            $score += 10;
        } else {
            $score -= 2;
        }
    }
    return $score;
}

// Inisialisasi sesi jika belum ada
if (!isset($_SESSION['game_over'])) {
    $_SESSION['game_over'] = false;
}

// Inisialisasi kata jika belum ada atau jika permainan baru dimulai
if (!isset($_SESSION['word']) || isset($_POST['play_again'])) {
    $_SESSION['word'] = getRandomWord($pdo);
    $_SESSION['game_over'] = false;
    unset($_SESSION['score']);
    unset($_SESSION['user_answer']);
}

$word = $_SESSION['word']['kata'];
$clue = $_SESSION['word']['clue'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['answer'])) {
    $userAnswer = strtoupper(implode('', $_POST['answer']));
    $score = calculateScore($userAnswer, $word);
    $_SESSION['score'] = $score;
    $_SESSION['game_over'] = true;
    $_SESSION['user_answer'] = $userAnswer;
}

if (isset($_POST['save_score']) && isset($_POST['username'])) {
    $stmt = $pdo->prepare("INSERT INTO point_game (nama_user, total_point) VALUES (:nama_user, :total_point)");
    $stmt->execute([
        'nama_user' => $_POST['username'],
        'total_point' => $_SESSION['score']
    ]);
    $_SESSION['word'] = getRandomWord($pdo);
    $_SESSION['game_over'] = false;
    unset($_SESSION['score']);
    unset($_SESSION['user_answer']);
    header("Location: index.php");
    exit;
}

if (isset($_POST['play_again'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asah Otak Game</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        h1 {
            color: #333;
            margin-bottom: 1rem;
        }
        .clue {
            font-weight: 600;
            color: #4a4a4a;
            margin-bottom: 1rem;
        }
        .word-container {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
        }
        input[type="text"] {
            width: 30px;
            height: 30px;
            text-align: center;
            margin: 0 2px;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-size: 18px;
        }
        input[type="text"]:focus {
            outline: none;
            border-color: #4CAF50;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #45a049;
        }
        .result {
            margin-top: 1rem;
            font-weight: 600;
        }
        .correct { color: #4CAF50; }
        .incorrect { color: #f44336; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Asah Otak Game</h1>
        <?php if (!$_SESSION['game_over']): ?>
            <p class="clue"><?php echo $clue; ?></p>
            <form method="post">
                <div class="word-container">
                    <?php
                    for ($i = 0; $i < strlen($word); $i++) {
                        if ($i == 2 || $i == 6) {
                            echo '<input type="text" name="answer[]" value="' . $word[$i] . '" readonly>';
                        } else {
                            echo '<input type="text" name="answer[]" maxlength="1" required>';
                        }
                    }
                    ?>
                </div>
                <button type="submit">Jawab</button>
            </form>
        <?php else: ?>
            <p class="result">Poin yang Anda dapat: <?php echo $_SESSION['score']; ?></p>
            <p>Jawaban yang benar: <?php echo $word; ?></p>
            <p>Jawaban Anda: 
                <?php
                for ($i = 0; $i < strlen($word); $i++) {
                    $class = ($_SESSION['user_answer'][$i] == $word[$i]) ? 'correct' : 'incorrect';
                    echo "<span class='$class'>{$_SESSION['user_answer'][$i]}</span>";
                }
                ?>
            </p>
            <form method="post">
                <button type="submit" name="save_score">Simpan Poin</button>
                <button type="submit" name="play_again">Main Lagi</button>
            </form>
        <?php endif; ?>
    </div>

    <script>
    <?php if (isset($_POST['save_score'])): ?>
    let username = prompt("Masukkan nama Anda:");
    if (username) {
        let form = document.createElement('form');
        form.method = 'post';
        form.innerHTML = '<input type="hidden" name="save_score" value="1"><input type="hidden" name="username" value="' + username + '">';
        document.body.appendChild(form);
        form.submit();
    }
    <?php endif; ?>

    document.addEventListener('DOMContentLoaded', (event) => {
        const inputs = document.querySelectorAll('input[type="text"]');
        inputs.forEach((input, index) => {
            input.addEventListener('input', function() {
                if (this.value.length === this.maxLength) {
                    if (inputs[index + 1]) {
                        inputs[index + 1].focus();
                    }
                }
            });
        });
    });
    </script>
</body>
</html>