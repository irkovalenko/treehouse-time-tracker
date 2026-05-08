<?php
require 'inc/functions.php';

$page = "tasks";
$pageTitle = "Task List | Time Tracker";

if (isset($_POST['Delete'])) {
    $task_id = $_POST['Delete'];
    deleteTask($task_id);
    header("Location: task_list.php?msg=Task+Deleted");
    exit();
}

if (isset($_GET['msg'])) {
    $error_message = trim($_GET['msg']);
}

include 'inc/header.php';
?>
<div class="section catalog random">

    <div class="col-container page-container">
        <div class="col col-70-md col-60-lg col-center">

            <h1 class="actions-header">Task List</h1>
            <div class="actions-item">
                <a class="actions-link" href="task.php">
                    <span class="actions-icon">
                        <svg viewbox="0 0 64 64">
                            <use xlink:href="#task_icon"></use>
                        </svg>
                    </span>
                    Add Task</a>
            </div>
            <?php
            if (isset($error_message)) {
                echo "<p class='message'>$error_message</p>";
            }
            ?>
            <div class="form-container">
                <ul class="items">
                    <?php
                    $tasks = getTasksList();
                    foreach ($tasks as $task) {
                        echo "<li class='item'>
                                    <span class='item-title'>
                                    <a href='task.php?task_id={$task['task_id']}'>{$task['task_title']}</a>
                                    </span>
                                    <form method='post' action='task_list.php' onsubmit=\"return confirm('Are you sure you want to delete this task?')\">
                                    <input type='hidden' value='{$task['task_id']}' name='Delete' />
                                    <input type='submit' value='Delete' class='button--delete' />
                                    </form>
                              </li>";
                    }
                    ?>
                </ul>
            </div>

        </div>
    </div>
</div>

<?php include("inc/footer.php"); ?>