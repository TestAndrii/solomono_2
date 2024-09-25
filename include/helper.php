<?php

class Helper
{
    /*
     * Checks database connection.
    */
    public static function getConnection()
    {
        $dsn = 'mysql:host=' . (defined('DB_HOST') ? DB_HOST : '') . ';dbname=' . (defined('DB_NAME') ? DB_NAME : '');

        try {
            $pdo = new PDO($dsn, (defined('DB_USER') ? DB_USER : 'default'), (defined('DB_PASS') ? DB_PASS : 'default'), []);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = $pdo->query('SHOW VARIABLES like "version"');
            $row = $query->fetch();

            echo '<p>MySQL version:' . $row['Value'] . "</p>";
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }

        return $pdo;
    }

    public static function push_dump()
    {
        $dsn = 'mysql:host=' . (defined('DB_HOST') ? DB_HOST : '') . ';dbname=' . (defined('DB_NAME') ? DB_NAME : '');

        try {
            $pdo = new PDO($dsn, (defined('DB_USER') ? DB_USER : 'default'), (defined('DB_PASS') ? DB_PASS : 'default'), []);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }

        $sql = file_get_contents(__DIR__ . '/../dump.sql');

        $statements = explode(';', $sql);

        foreach ($statements as $statement) {
            if (!empty(trim($statement))) {
                $pdo->exec($statement);
            }
        }
    }

    public static function drop_table()
    {
        $dsn = 'mysql:host=' . (defined('DB_HOST') ? DB_HOST : '') . ';dbname=' . (defined('DB_NAME') ? DB_NAME : '');

        try {
            $pdo = new PDO($dsn, (defined('DB_USER') ? DB_USER : 'default'), (defined('DB_PASS') ? DB_PASS : 'default'), []);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }

        $sql = "DROP TABLE categories";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }

    public static function buildTree(array $categories)
    {
        $outTree = [];

        foreach ($categories as $category) {
            $categories_id = $category['categories_id'];
            $parent_id = $category['parent_id'];

            if (isset($outTree[$categories_id])) {
                $outTree[$parent_id][$categories_id] = $outTree[$categories_id];
                unset($outTree[$categories_id]);
            } else {
                $outTree[$parent_id][$categories_id] = $categories_id;
            }
        }

        return $outTree[0];
    }
}