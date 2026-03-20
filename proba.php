<?php
require_once __DIR__ . '/init.php';

$postsFile = 'data/posts.json';

// USER ADATOK BETÖLTÉSE
$userData = null;
if (isset($_SESSION['user_name'])) {
    $stmt = mysqli_prepare($conn, "SELECT uusername, uname, uavatar FROM felhasznalok WHERE uusername = ?");
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['user_name']);
    mysqli_stmt_execute($stmt);
    $userData = mysqli_stmt_get_result($stmt)->fetch_assoc();
}

// POSZT MENTÉS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userData) {

    $posts = file_exists($postsFile) ? json_decode(file_get_contents($postsFile), true) : [];

    // KÉP FELTÖLTÉS
    $img = "";
    if (!empty($_FILES['image']['name'])) {
        $img = time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "img/posts/" . $img);
    }

    $posts[] = [
        "user" => $userData['uusername'],
        "name" => $userData['uname'],
        "avatar" => $userData['uavatar'] ?? "default.png",
        "text" => $_POST['text'],
        "image" => $img,
        "time" => date("H:i"),
        "likes" => 0
    ];

    file_put_contents($postsFile, json_encode($posts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header("Location: blog.php");
    exit();
}

// LIKE
if (isset($_GET['like'])) {
    $id = $_GET['like'];
    $posts = json_decode(file_get_contents($postsFile), true);
    if (isset($posts[$id])) {
        $posts[$id]['likes']++;
        file_put_contents($postsFile, json_encode($posts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    header("Location: blog.php");
    exit();
}

$posts = file_exists($postsFile) ? json_decode(file_get_contents($postsFile), true) : [];
?>

<!DOCTYPE html>
<html lang="hu">
<head>
<meta charset="UTF-8">
<title>Messenger Blog</title>

<style>
body {
    margin: 0;
    font-family: Arial;
    height: 100vh;
    overflow: hidden;
    background: #e5ddd5;
}

/* KERET */
.container {
    display: flex;
    flex-direction: column;
    height: 100vh;
    max-width: 500px;
    margin: auto;
    background: #fff;
}

/* HEADER */
.header {
    padding: 10px;
    background: #0084ff;
    color: white;
    font-size: 18px;
}

/* CHAT */
.chat {
    flex: 1;
    overflow-y: auto;
    padding: 10px;
}

/* ÜZENET */
.msg {
    display: flex;
    margin: 8px 0;
}

.left { justify-content: flex-start; }
.right { justify-content: flex-end; }

.bubble {
    max-width: 70%;
    padding: 10px;
    border-radius: 15px;
    position: relative;
    font-size: 16px;
}

.left .bubble {
    background: #f1f0f0;
}

.right .bubble {
    background: #0084ff;
    color: white;
}

.name {
    font-size: 12px;
    opacity: 0.6;
}

/* AVATAR */
.avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    margin: 0 5px;
}

/* KÉP */
.msg img.postimg {
    max-width: 100%;
    border-radius: 10px;
    margin-top: 5px;
}

/* LIKE */
.like {
    font-size: 12px;
    margin-top: 5px;
    cursor: pointer;
}

/* FOOTER */
.footer {
    padding: 8px;
    background: #f0f0f0;
}

form {
    display: flex;
    gap: 5px;
}

input[type=text] {
    flex: 1;
    padding: 8px;
}

button {
    padding: 8px;
    background: #0084ff;
    color: white;
    border: none;
}

/* MOBIL */
@media(max-width:600px){
    .bubble { font-size: 16px; }
}
</style>

</head>
<body>

<div class="container">

<div class="header">💬 Messenger Blog</div>

<div class="chat" id="chat">

<?php foreach ($posts as $i => $p):
    $mine = ($userData && $p['user'] === $userData['uusername']);
?>

<div class="msg <?php echo $mine ? 'right' : 'left'; ?>">

<?php if(!$mine): ?>
    <img src="img/profiles/<?php echo $p['avatar']; ?>" class="avatar">
<?php endif; ?>

<div class="bubble">

<?php if(!$mine): ?>
<div class="name"><?php echo htmlspecialchars($p['name']); ?></div>
<?php endif; ?>

<?php echo htmlspecialchars($p['text']); ?>

<?php if(!empty($p['image'])): ?>
<img src="img/posts/<?php echo $p['image']; ?>" class="postimg">
<?php endif; ?>

<div class="like">
❤️ <?php echo $p['likes']; ?>
<a href="?like=<?php echo $i; ?>">Like</a>
</div>

</div>

<?php if($mine): ?>
    <img src="img/profiles/<?php echo $p['avatar']; ?>" class="avatar">
<?php endif; ?>

</div>

<?php endforeach; ?>

</div>

<div class="footer">
<form method="POST" enctype="multipart/form-data">
<input type="text" name="text" placeholder="Írj üzenetet..." required>
<input type="file" name="image">
<button>Küld</button>
</form>
</div>

</div>

<script>
// AUTO SCROLL ALJÁRA
let chat = document.getElementById("chat");
chat.scrollTop = chat.scrollHeight;
</script>

</body>
</html>
