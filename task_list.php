<?php
require 'inc/functions.php';

$page = "tasks";
$pageTitle = "Task List | Time Tracker";

include 'inc/header.php';
?>
<div class="section catalog random">

    <div class="col-container page-container">
        <div class="col col-70-md col-60-lg col-center">

            <h1 class="actions-header">Task List</h1>
            <div class="actions-item">
                <a class="actions-link" href="task.php">
                    <span class="actions-icon">
                        <svg viewbox="0 0 64 64"><use xlink:href="#task_icon"></use></svg>
                    </span>
                Add Task</a>
            </div>

            <div class="form-container">
              <ul class="items">
                <?php
                    $tasks = getTasksList();
                    foreach ($tasks as $task) {
                        echo "<li class='item'>
                                    <span class='item-title'>{$task['task_title']}</span>
                                    <span class='item-project'>{$task['project_title']}</span>
                                    <span class='item-date'>{$task['date']}</span>
                                    <span class='item-time'>{$task['time']} min</span>
                              </li>";
                    }
                ?>
              </ul>
            </div>

        </div>
    </div>
</div>

<?php include("inc/footer.php"); ?>
