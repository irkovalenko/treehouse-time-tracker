<?php

function getProjectList()
{
    include_once 'Database.php';
    $db = new Database();
    $statement = $db->connection->query(
        "SELECT project_id, title, category FROM projects"
    );
    try {
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        die("Error fetching projects: " . $e->getMessage());
    }
}

function addProject($title, $category, $project_id = null)
{
    include_once 'Database.php';
    $db = new Database();
    if ($project_id) {
        $sql = "UPDATE projects SET title = :title, category = :category WHERE project_id = :project_id";
    } else {
        $sql = "INSERT INTO projects (title, category) VALUES (:title, :category)";
    }
    try {
        $statement = $db->connection->prepare($sql);
        $statement->bindParam(':title', $title, PDO::PARAM_STR);
        $statement->bindParam(':category', $category, PDO::PARAM_STR);
        if ($project_id) {
            $statement->bindParam(':project_id', $project_id, PDO::PARAM_INT);
        }
        return $statement->execute();
    } catch (Exception $e) {
        die("Error adding project: " . $e->getMessage());
    }
}

function getTasksList($filter = null)
{
    include_once 'Database.php';
    $db = new Database();

    $sql =
        "SELECT task_id, tasks.title AS task_title, projects.title AS project_title, date, time FROM tasks
         JOIN projects ON tasks.project_id = projects.project_id";

    $filterType = null;
    $filterValue = null;

    if (!empty($filter)) {
        if (strpos($filter, 'date:') === 0) {
            $dateFilter = substr($filter, 5);
            $filterType = 'date';
            if ($dateFilter === 'today') {
                $today = date('d/m/Y');
                $filterValue = ['start_date' => $today, 'end_date' => $today];
            } elseif ($dateFilter === 'week') {
                $startOfWeek = date('d/m/Y', strtotime('monday this week'));
                $endOfWeek = date('d/m/Y', strtotime('sunday this week'));
                $filterValue = ['start_date' => $startOfWeek, 'end_date' => $endOfWeek];
            } elseif ($dateFilter === 'month') {
                $startOfMonth = date('d/m/Y', strtotime('first day of this month'));
                $endOfMonth = date('d/m/Y', strtotime('last day of this month'));
                $filterValue = ['start_date' => $startOfMonth, 'end_date' => $endOfMonth];
            }
        } elseif (strpos($filter, 'category:') === 0) {
            $filterType = 'category';
            $filterValue = substr($filter, 9);
        } else {
            $filterType = 'project';
            $filterValue = substr($filter, 8);
        }
    }

    if ($filterType === 'project') {
        $sql .= " WHERE tasks.project_id = :project_id";
    } elseif ($filterType === 'category') {
        $sql .= " WHERE projects.category = :category";
    } elseif ($filterType === 'date') {
        $sql .= " WHERE STR_TO_DATE(date, '%d/%m/%Y') >= STR_TO_DATE(:start_date, '%d/%m/%Y')
AND STR_TO_DATE(date, '%d/%m/%Y') <= STR_TO_DATE(:end_date, '%d/%m/%Y')";
    }

    $sql .= " ORDER BY projects.title, date DESC";
    $statement = $db->connection->prepare($sql);

    if ($filterType === 'project') {
        $statement->bindValue(':project_id', $filterValue, PDO::PARAM_INT);
    } elseif ($filterType === 'category') {
        $statement->bindValue(':category', $filterValue, PDO::PARAM_STR);
    } elseif ($filterType === 'date') {
        $statement->bindValue(':start_date', $filterValue['start_date'], PDO::PARAM_STR);
        $statement->bindValue(':end_date', $filterValue['end_date'], PDO::PARAM_STR);
    }

    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function addTask($title, $project_id, $date, $time, $task_id = null)
{
    include_once 'Database.php';
    $db = new Database();
    if ($task_id) {
        $sql = "UPDATE tasks SET title = :title, project_id = :project_id, date = :date, time = :time WHERE task_id = :task_id";
    } else {
        $sql = "INSERT INTO tasks (title, project_id, date, time) VALUES (:title, :project_id, :date, :time)";
    }
    try {
        $statement = $db->connection->prepare($sql);
        $statement->bindParam(':title', $title, PDO::PARAM_STR);
        $statement->bindParam(':project_id', $project_id, PDO::PARAM_INT);
        $statement->bindParam(':date', $date, PDO::PARAM_STR);
        $statement->bindParam(':time', $time, PDO::PARAM_INT);
        if ($task_id) {
            $statement->bindParam(':task_id', $task_id, PDO::PARAM_INT);
        }
        return $statement->execute();
    } catch (Exception $e) {
        die("Error adding task: " . $e->getMessage());
    }
}

function getProject(string $project_id)
{
    include_once 'Database.php';
    $db = new Database();
    $sql = "SELECT EXISTS( SELECT * FROM projects WHERE project_id = :project_id);";
    try {
        $statement = $db->connection->prepare($sql);
        $statement->bindParam(':project_id', $project_id, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetch();
    } catch (Exception $e) {
        die("Error fetching project: " . $e->getMessage());
    }
}

function getTask(string $task_id)
{
    include_once 'Database.php';
    $db = new Database();
    $sql = "SELECT task_id, title, date, time, project_id FROM tasks WHERE task_id = :task_id";
    try {
        $statement = $db->connection->prepare($sql);
        $statement->bindParam(':task_id', $task_id, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetch();
    } catch (Exception $e) {
        die("Error fetching task: " . $e->getMessage());
    }
}

function deleteTask(string $task_id)
{
    include_once 'Database.php';
    $db = new Database();
    $sql = "DELETE FROM tasks WHERE task_id = :task_id";
    try {
        $statement = $db->connection->prepare($sql);
        $statement->bindParam(':task_id', $task_id, PDO::PARAM_INT);
        $statement->execute();
    } catch (Exception $e) {
        die("Error fetching task: " . $e->getMessage());
    }
}

function deleteProject(string $project_id)
{
    include_once 'Database.php';
    $db = new Database();
    $sql = "DELETE FROM projects WHERE project_id = :project_id AND
            project_id NOT IN (SELECT project_id FROM tasks); ";
    try {
        $statement = $db->connection->prepare($sql);
        $statement->bindParam(':project_id', $project_id, PDO::PARAM_INT);
        $statement->execute();
    } catch (Exception $e) {
        die("Error fetching project: " . $e->getMessage());
    }
}
