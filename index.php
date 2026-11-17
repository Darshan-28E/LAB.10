
<?php
session_start();
date_default_timezone_set("Asia/Kolkata");

$page = "home";
$message = "";
$duration = "";

// PROGRAM A: LOGIN
if (isset($_POST['login'])) {
    $name = trim($_POST['name']);

    if ($name != "") {
        $_SESSION['name'] = $name;
        $_SESSION['start_time'] = time();
        $page = "hello";
    }
}

// PROGRAM A: LOGOUT
if (isset($_POST['logout'])) {
    if (isset($_SESSION['start_time'])) {
        $seconds = time() - $_SESSION['start_time'];

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        $duration = "$hours hours, $minutes minutes, $secs seconds";
        $logoutName = $_SESSION['name'];
    }

    session_unset();
    session_destroy();
    $page = "logout";
}

// PROGRAM C: AGE VALIDATION
if (isset($_POST['check_age'])) {
    $name = trim($_POST['person_name']);
    $age = filter_var($_POST['age'], FILTER_VALIDATE_INT);

    if ($name != "" && $age !== false && $age >= 0) {
        if ($age < 18) {
            $message = "Hello, " . htmlspecialchars($name) .
                       ", you are not authorized to visit the site.";
        } else {
            $message = "Welcome to this site, " .
                       htmlspecialchars($name) . "!";
        }
        $page = "age_result";
    } else {
        $message = "Please enter a valid name and age.";
        $page = "age_result";
    }
}

// PAGE NAVIGATION
if (isset($_GET['page']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $page = $_GET['page'];
}

// Protect hello page
if ($page == "hello" && !isset($_SESSION['name'])) {
    $page = "home";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>PHP Web Applications</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #edf1f7;
            color: #26334d;
        }

        header {
            background: #243b64;
            color: white;
            text-align: center;
            padding: 25px;
        }

        header h1 {
            margin: 0;
            font-size: 30px;
        }

        nav {
            background: #172b4d;
            text-align: center;
            padding: 15px;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin: 12px;
            font-weight: bold;
        }

        nav a:hover {
            color: #8fc5ff;
        }

        .container {
            max-width: 850px;
            margin: 45px auto;
            padding: 35px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 20px #0002;
        }

        h2 {
            color: #243b64;
            text-align: center;
        }

        p {
            text-align: center;
            line-height: 1.7;
        }

        input {
            display: block;
            width: 100%;
            padding: 14px;
            margin: 15px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 14px;
            background: #243b64;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #4269a8;
        }

        .logout {
            background: #d94343;
            margin-top: 20px;
        }

        .logout:hover {
            background: #b82f2f;
        }

        .start-time {
            text-align: right;
            color: #243b64;
            font-weight: bold;
            font-size: 14px;
        }

        .result {
            background: #eaf2ff;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
            font-size: 18px;
        }

        footer {
            text-align: center;
            padding: 20px;
            color: #777;
        }

        .links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-top: 25px;
        }

        .links a {
            background: #243b64;
            color: white;
            padding: 20px;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
        }

        .links a:hover {
            background: #4269a8;
        }
    </style>
</head>

<body>

<header>
    <h1>PHP Web Applications</h1>
    <p>PHP Programming Lab</p>
</header>

<nav>
    <a href="index.php">Home</a>
    <a href="index.php?page=date">Date & Time</a>
    <a href="index.php?page=age">Age Validation</a>
</nav>

<div class="container">

<?php if ($page == "home") { ?>

    <h2>Welcome to PHP Applications</h2>
    <p>Select a program to continue.</p>

    <div class="links">
        <a href="index.php?page=login">A. Session Application</a>
        <a href="index.php?page=date">B. Date & Time</a>
        <a href="index.php?page=age">C. Age Validation</a>
    </div>

<?php } ?>

<?php if ($page == "login") { ?>

    <h2>Login Page</h2>
    <p>Enter your name to start your session.</p>

    <form method="POST" action="index.php">
        <input type="text" name="name"
               placeholder="Enter your name" required>

        <button type="submit" name="login">
            Submit
        </button>
    </form>

<?php } ?>

<?php if ($page == "hello") { ?>

    <div class="start-time">
        Start Time:
        <?php echo date("h:i:s A", $_SESSION['start_time']); ?>
    </div>

    <h2>Hello, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>

    <div class="result">
        Welcome! Your session has started successfully.
    </div>

    <form method="POST" action="index.php">
        <button type="submit" name="logout" class="logout">
            Logout
        </button>
    </form>

<?php } ?>

<?php if ($page == "logout") { ?>

    <h2>Thank You!</h2>

    <div class="result">
        <h3>Thank you, <?php echo htmlspecialchars($logoutName ?? "User"); ?>!</h3>

        <p>You have successfully logged out.</p>

        <p>
            Your total usage duration was:
            <strong><?php echo htmlspecialchars($duration); ?></strong>
        </p>
    </div>

    <p><a href="index.php?page=login">Login Again</a></p>

<?php } ?>

<?php if ($page == "date") { ?>

    <h2>Current Date and Time</h2>

    <div class="result">
        <p>
            <strong>Current Date:</strong>
            <?php echo date("d-m-Y"); ?>
        </p>

        <p>
            <strong>Current Time:</strong>
            <?php echo date("h:i:s A"); ?>
        </p>

        <p>
            <strong>Current Day:</strong>
            <?php echo date("l"); ?>
        </p>
    </div>

<?php } ?>

<?php if ($page == "age") { ?>

    <h2>Age Verification</h2>

    <p>Enter your name and age to check authorization.</p>

    <form method="POST" action="index.php">
        <input type="text" name="person_name"
               placeholder="Enter your name" required>

        <input type="number" name="age"
               placeholder="Enter your age"
               min="0" required>

        <button type="submit" name="check_age">
            Check Age
        </button>
    </form>

<?php } ?>

<?php if ($page == "age_result") { ?>

    <h2>Verification Result</h2>

    <div class="result">
        <?php echo $message; ?>
    </div>

    <p>
        <a href="index.php?page=age">Try Again</a>
    </p>

<?php } ?>

</div>

<footer>
    PHP Lab Assignment | Employee & Session Web Applications
</footer>

</body>
</html>
 