<?php

function getProjectList() {
    include_once 'Database.php';
    $db = new Database();
    $statement = $db->connection->query(
        "SELECT project_id, title, category FROM projects");
        try {
        return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (Exception $e) {
            die("Error fetching projects: " . $e->getMessage());
        }
}

function addProject($title, $category) {
    include_once 'Database.php';
    $db = new Database();
    $sql = "INSERT INTO projects (title, category) VALUES (:title, :category)";
    try {
        $statement = $db->connection->prepare($sql);
        $statement->bindParam(':title', $title, PDO::PARAM_STR);
        $statement->bindParam(':category', $category, PDO::PARAM_STR);
        return $statement->execute();
    } catch (Exception $e) {
        die("Error adding project: " . $e->getMessage());
    }

}

function getTasksList($filter = null) {
    include_once 'Database.php';
    $db = new Database();

    $sql =
        "SELECT task_id, tasks.title AS task_title, projects.title AS project_title, date, time FROM tasks
         JOIN projects ON tasks.project_id = projects.project_id";

    $filterType = null;
    $filterValue = null;

    if (!empty($filter)) {
        if (strpos($filter, 'category:') === 0) {
            $filterType = 'category';
            $filterValue = substr($filter, 9);
        } else {
            $filterType = 'project';
            $filterValue = $filter;
        }
    }

    if ($filterType === 'project') {
        $sql .= " WHERE tasks.project_id = :project_id";
    } elseif ($filterType === 'category') {
        $sql .= " WHERE projects.category = :category";
    }

    $sql .= " ORDER BY projects.title, date DESC";
    $statement = $db->connection->prepare($sql);

    if ($filterType === 'project') {
        $statement->bindValue(':project_id', $filterValue, PDO::PARAM_INT);
    } elseif ($filterType === 'category') {
        $statement->bindValue(':category', $filterValue, PDO::PARAM_STR);
    }

    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function addTask($title, $project_id, $date, $time) {
    include_once 'Database.php';
    $db = new Database();
    $sql = "INSERT INTO tasks (title, project_id, date, time) VALUES (:title, :project_id, :date, :time)";
    try {
        $statement = $db->connection->prepare($sql);
        $statement->bindParam(':title', $title, PDO::PARAM_STR);
        $statement->bindParam(':project_id', $project_id, PDO::PARAM_INT);
        $statement->bindParam(':date', $date, PDO::PARAM_STR);
        $statement->bindParam(':time', $time, PDO::PARAM_INT);
        return $statement->execute();
    } catch (Exception $e) {
        die("Error adding task: " . $e->getMessage());
    }

}
