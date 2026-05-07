<?php
require 'inc/functions.php';

$pageTitle = "Task | Time Tracker";
$page = "tasks";
$projectId = $task_id = $title = $date = $time = ''; //setting the variables to empty before submitting the form


if (isset($_GET['task_id'])) {
    list($task_id, $title, $date, $time, $project_id) = getTask($_GET['task_id']);
     $projectId = $project_id;
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $task_id = $_POST['task_id'] ?? '';
    $title = $_POST['title'] ?? ''; // raw input with no html escaping
    $projectId = $_POST['project_id'] ?? '';
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $title = trim(htmlspecialchars($title, ENT_QUOTES, 'UTF-8'));
    $date = trim(htmlspecialchars($date, ENT_QUOTES, 'UTF-8'));
    $time = trim(htmlspecialchars($time, ENT_QUOTES, 'UTF-8'));

    $dateMatch = explode('/', $date);

    if (empty($title) || empty($projectId) || empty($date) || empty($time)) {
        $error_message = "All fields are required.";
    }
    // date validation

    elseif (count($dateMatch) !==3 // if has 3 elements separated by /
    || strlen($dateMatch[0]) !== 2 // if first element (day) is not 2 characters - dd
    || strlen($dateMatch[1]) !== 2
    || strlen($dateMatch[2]) !== 4
    || !checkdate((int)$dateMatch[0], (int)$dateMatch[1], (int)$dateMatch[2])) { // checking the valid date so it prevents to select 02/30/2024 for example
        $error_message = "Please enter the date in the correct format (mm/dd/yyyy).";
    }
    else
{
    if (addTask($title, $projectId, $date, $time, $task_id)) {
        header ("Location: task_list.php");
        exit();
    } else {
        $error_message = "Error: Could not add task.";
    }
}
}

include 'inc/header.php';
?>

<div class="section page">
    <div class="col-container page-container">
        <div class="col col-70-md col-60-lg col-center">
            <h1 class="actions-header"><?php
                 if (!empty($task_id)) {
                echo "Edit Task";
            } else {
                echo "Add Task";
            }
            ?>
            </h1>
            <?php
            if (isset($error_message)) {
                echo "<p class='message'>$error_message</p>";
            }

            ?>
            <form class="form-container form-add" method="post" action="task.php">
                <table>
                    <tr>
                        <th>
                            <label for="project_id">Project</label>
                        </th>
                        <td>
                            <select name="project_id" id="project_id">
                                <option value="">Select One</option>
                                         <?php
                    $projects = getProjectList();
                    foreach ($projects as $project) {
                        if ($projectId == $project['project_id']) {
                            echo "<option value='{$project['project_id']}' selected>{$project['title']}</option>";
                        } else {
                        echo "<option value='{$project['project_id']}'>{$project['title']}</option>";
                    }
                    }


?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="title">Title<span class="required">*</span></label></th>
                        <td><input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>" /></td>
                    </tr>
                    <tr>
                        <th><label for="date">Date<span class="required">*</span></label></th>
                        <td><input type="text" id="date" name="date" value="<?php echo htmlspecialchars($date, ENT_QUOTES, 'UTF-8'); ?>" placeholder="mm/dd/yyyy" /></td>
                    </tr>
                    <tr>
                        <th><label for="time">Time<span class="required">*</span></label></th>
                        <td><input type="text" id="time" name="time" value="<?php echo htmlspecialchars($time, ENT_QUOTES, 'UTF-8'); ?>" /> minutes</td>
                    </tr>
                </table>
                <?php
                if (!empty($task_id)) {
                    echo "<input type='hidden' name='task_id' value='$task_id' />";
                }
                ?>
                <input class="button button--primary button--topic-php" type="submit" value="Submit" />
            </form>
        </div>
    </div>
</div>

<?php include "inc/footer.php"; ?>
