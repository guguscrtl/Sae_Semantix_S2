<!DOCTYPE html>
<html>
<head>
    <title>Logs</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../style/leaderboard.css">
    <link rel="stylesheet" href="../style/nav.css">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script type="text/javascript" src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>
</head>
<body style="background-color: #A5A77D">

<nav class="navbar" style="margin-left: 0%; margin-right: 35%; margin-bottom: 2%; width: 5%">
    <ul>
        <li><a href="../connexion/deconnexion.php"><img src="https://cdn-icons-png.flaticon.com/512/25/25376.png" title="Déconnexion"></a></li>
    </ul>
</nav>

<div class="container mt-5">
    <h2 class="text-center">Logs</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Log ID</th>
                    <th>User ID</th>
                    <th>Action</th>
                    <th>Date</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php
                session_start();

                try {
                    $conn = new PDO('mysql:host=localhost;dbname=matis.vivier_db', 'root', '');;
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $sql = "SELECT * FROM logs ORDER BY log_date DESC LIMIT 500";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<tr>';
                        echo '<td>' . $row['log_id'] . '</td>';
                        echo '<td>' . $row['user_id'] . '</td>';
                        echo '<td>' . $row['action'] . '</td>';
                        echo '<td>' . $row['log_date'] . '</td>';
                        echo '<td>' . $row['ip_address'] . '</td>';
                        echo '</tr>';
                    }
                } catch (PDOException $e) {
                    echo "Erreur : " . $e->getMessage();
                } finally {
                    $conn = null;
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
