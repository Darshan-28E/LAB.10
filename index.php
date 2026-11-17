<?php
session_start();
date_default_timezone_set('Asia/Kolkata');

$page = $_GET['page'] ?? 'home';
$message = '';
$messageType = 'info';
$durationText = '';

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Program A: start a session and store the user's name and start time.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    $name = trim($_POST['name'] ?? '');
    if ($name !== '') {
        session_regenerate_id(true);
        $_SESSION['name'] = $name;
        $_SESSION['start_time'] = time();
        header('Location: ?page=hello');
        exit;
    }
    $page = 'home';
    $message = 'Please enter your name to start the session.';
    $messageType = 'error';
}

// Program A: calculate usage duration and end the session.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'logout') {
    if (isset($_SESSION['start_time'], $_SESSION['name'])) {
        $seconds = max(0, time() - (int)$_SESSION['start_time']);
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $remainingSeconds = $seconds % 60;
        $durationText = sprintf('%02d:%02d:%02d', $hours, $minutes, $remainingSeconds);
        $_SESSION['last_name'] = $_SESSION['name'];
        $_SESSION['last_duration'] = $durationText;
    }
    unset($_SESSION['name'], $_SESSION['start_time']);
    session_regenerate_id(true);
    header('Location: ?page=logout');
    exit;
}

// Protect the hello page: only an active session can view it.
if ($page === 'hello' && (!isset($_SESSION['name'], $_SESSION['start_time']))) {
    header('Location: ?page=home');
    exit;
}

// Program C: validate age submitted from the HTML form.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'age_check') {
    $name = trim($_POST['name'] ?? '');
    $ageInput = trim($_POST['age'] ?? '');

    if ($name === '' || $ageInput === '' || filter_var($ageInput, FILTER_VALIDATE_INT) === false || (int)$ageInput < 0) {
        $page = 'age';
        $message = 'Please enter a valid name and a non-negative whole-number age.';
        $messageType = 'error';
    } else {
        $age = (int)$ageInput;
        $page = 'age_result';
        if ($age < 18) {
            $message = 'Hello, ' . $name . ', you are not authorized to visit the site.';
            $messageType = 'warning';
        } else {
            $message = 'Welcome to this site, ' . $name . '!';
            $messageType = 'success';
        }
    }
}

if ($page === 'logout') {
    $logoutName = $_SESSION['last_name'] ?? 'User';
    $durationText = $_SESSION['last_duration'] ?? '00:00:00';
    unset($_SESSION['last_name'], $_SESSION['last_duration']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PHP Web Applications | LAB 10</title>
<style>
:root{--navy:#172b4d;--blue:#24416d;--bg:#edf2f8;--ink:#243247;--muted:#68778c;--line:#e4eaf2}
*{box-sizing:border-box}
body{margin:0;font-family:Arial,Helvetica,sans-serif;background:linear-gradient(135deg,#eaf0f8,#f5f7fb);color:var(--ink);min-height:100vh}
header{background:var(--navy);color:#fff;padding:24px 20px;text-align:center}
header h1{margin:0;font-size:30px;letter-spacing:.2px}
header p{margin:8px 0 0;color:#cbd6e6}
nav{display:flex;justify-content:center;gap:10px;flex-wrap:wrap;background:#10213d;padding:13px}
nav a{color:#fff;text-decoration:none;padding:10px 17px;border-radius:8px;font-weight:600}
nav a:hover,nav a.active{background:#365a91}
main{width:min(960px,calc(100% - 32px));margin:42px auto;background:#fff;border-radius:18px;padding:34px;box-shadow:0 12px 36px rgba(28,48,78,.10)}
.eyebrow{color:#5274a5;text-transform:uppercase;letter-spacing:1.5px;font-size:12px;font-weight:700}
h2{color:var(--navy);margin:10px 0 8px;font-size:27px}
.lead{color:var(--muted);line-height:1.6;margin:0 0 25px}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:16px;margin:25px 0}
.tile{border:1px solid var(--line);border-radius:13px;padding:20px;background:#fbfcfe}
.tile strong{display:block;color:var(--navy);font-size:17px;margin-bottom:8px}
.tile span{color:var(--muted);font-size:14px;line-height:1.5}
form{max-width:560px}
label{display:block;font-weight:700;margin:16px 0 8px;color:#33445c}
input{width:100%;padding:14px 15px;border:1px solid #cfd8e5;border-radius:9px;font-size:16px;outline:none}
input:focus{border-color:#5279b5;box-shadow:0 0 0 3px rgba(82,121,181,.13)}
button,.button{display:inline-block;border:0;border-radius:9px;background:var(--blue);color:#fff;padding:13px 22px;font-size:15px;font-weight:700;text-decoration:none;cursor:pointer;margin-top:18px}
button:hover,.button:hover{background:#345b93}
.secondary{background:#e9eef6;color:var(--navy)}
.secondary:hover{background:#dce5f1}
.notice{padding:15px 17px;border-radius:10px;margin:20px 0;line-height:1.5}
.info{background:#edf4ff;color:#254c83}
.success{background:#eaf8ef;color:#176b39}
.warning{background:#fff5df;color:#8a5a00}
.error{background:#fff0f0;color:#a12b2b}
.meta{display:flex;justify-content:flex-end;color:#fff;font-size:14px;margin-bottom:18px}
.meta span{background:#24416d;padding:9px 13px;border-radius:8px}
.clock{font-size:clamp(28px,5vw,42px);font-weight:800;color:#24416d;margin:18px 0}
footer{text-align:center;color:#7c899a;padding:0 15px 28px;font-size:13px}
.actions{display:flex;gap:10px;flex-wrap:wrap}
@media(max-width:600px){main{padding:23px;margin:24px auto}header h1{font-size:24px}.meta{justify-content:flex-start}}
</style>
</head>
<body>
<header>
    <h1>PHP Web Applications</h1>
    <p>PHP Programming Lab · Assignment 10</p>
</header>
<nav>
    <a class="<?= in_array($page,['home','hello','logout'],true)?'active':'' ?>" href="?page=home">Home &amp; Session</a>
    <a class="<?= $page==='clock'?'active':'' ?>" href="?page=clock">Date &amp; Time</a>
    <a class="<?= in_array($page,['age','age_result'],true)?'active':'' ?>" href="?page=age">Age Validation</a>
</nav>

<main>
<?php if ($page === 'home'): ?>
    <div class="eyebrow">Program A · Session management</div>
    <h2>Welcome to the Session Application</h2>
    <p class="lead">Enter your name to begin. Your session start time is stored using PHP sessions.</p>
    <?php if ($message): ?><div class="notice <?= e($messageType) ?>"><?= e($message) ?></div><?php endif; ?>
    <form method="post" action="?page=home">
        <input type="hidden" name="action" value="login">
        <label for="name">Your name</label>
        <input id="name" name="name" type="text" maxlength="80" placeholder="Enter your name" required>
        <button type="submit">Start Session</button>
    </form>

<?php elseif ($page === 'hello'): ?>
    <div class="meta"><span>Start Time: <?= e(date('h:i:s A', (int)$_SESSION['start_time'])) ?></span></div>
    <div class="eyebrow">Program A · Active session</div>
    <h2>Hello, <?= e($_SESSION['name']) ?>!</h2>
    <p class="lead">Your session is active. The start time is displayed in the top-right corner.</p>
    <div class="notice success">You have successfully logged in.</div>
    <form method="post" action="?page=hello">
        <input type="hidden" name="action" value="logout">
        <button type="submit">Logout</button>
    </form>

<?php elseif ($page === 'logout'): ?>
    <div class="eyebrow">Program A · Session ended</div>
    <h2>Thank You, <?= e($logoutName) ?>!</h2>
    <p class="lead">You have successfully logged out of the application.</p>
    <div class="notice info">Your total duration of usage was <strong><?= e($durationText) ?></strong> (HH:MM:SS).</div>
    <a class="button" href="?page=home">Start Again</a>

<?php elseif ($page === 'clock'): ?>
    <div class="eyebrow">Program B · PHP date and time</div>
    <h2>Current Date, Time and Day</h2>
    <p class="lead">The following values are generated by the PHP date() function using the Asia/Kolkata timezone.</p>
    <div class="grid">
        <div class="tile"><strong>Current Date</strong><span><?= e(date('d F Y')) ?></span></div>
        <div class="tile"><strong>Current Time</strong><span><?= e(date('h:i:s A')) ?></span></div>
        <div class="tile"><strong>Day of the Week</strong><span><?= e(date('l')) ?></span></div>
    </div>
    <p class="notice info">Server timezone: Asia/Kolkata (Indian Standard Time).</p>

<?php elseif ($page === 'age' || $page === 'age_result'): ?>
    <div class="eyebrow">Program C · Age validation</div>
    <h2>Age Verification</h2>
    <p class="lead">Enter your name and age. Visitors below 18 are not authorized to visit the site.</p>
    <?php if ($message): ?><div class="notice <?= e($messageType) ?>"><?= e($message) ?></div><?php endif; ?>
    <form method="post" action="?page=age">
        <input type="hidden" name="action" value="age_check">
        <label for="age_name">Name</label>
        <input id="age_name" name="name" type="text" maxlength="80" placeholder="Enter your name" required>
        <label for="age">Age</label>
        <input id="age" name="age" type="number" min="0" max="120" placeholder="Enter your age" required>
        <button type="submit">Check Eligibility</button>
    </form>
    <?php if ($page === 'age_result'): ?><a class="button secondary" href="?page=age">Check Another Person</a><?php endif; ?>

<?php else: ?>
    <div class="eyebrow">Page not found</div>
    <h2>Sorry, this page does not exist.</h2>
    <a class="button" href="?page=home">Return Home</a>
<?php endif; ?>
</main>
<footer>PHP Web Applications · Programs A, B and C</footer>
</body>
</html>
