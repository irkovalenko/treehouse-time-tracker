<?php
require 'inc/functions.php';

$page = "projects";
$pageTitle = "Project List | Time Tracker";

if (isset($_POST['Delete'])) {
    $project_id = $_POST['Delete'];
    deleteProject($project_id);
    header("Location: project_list.php?msg=Project+Deleted");
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
            <h1 class="actions-header">Project List</h1>
            <div class="actions-item">
                <a class="actions-link" href="project.php">
                    <span class="actions-icon">
                        <svg viewbox="0 0 64 64">
                            <use xlink:href="#project_icon"></use>
                        </svg>
                    </span>
                    Add Project
                </a>
            </div>

            <div class="form-container">
                <ul class="items">
                    <?php
                    $projects = getProjectList();
                    foreach ($projects as $project) {
                        echo "<li class='item'>
                                    <span class='item-title'>
                                    <a href='project.php?project_id={$project['project_id']}'>
                                    {$project['title']}</a>
                                    <form method='post' action='project_list.php' onsubmit=\"return confirm('Are you sure you want to delete this project?')\">
                                    <input type='hidden' value='{$project['project_id']}' name='Delete' />
                                    <input type='submit' value='Delete' class='button--delete' />
                                    </form>
                                    </span>
                                
                              </li>";
                    }


                    ?>

                </ul>
            </div>
        </div>
    </div>

</div>

<?php include("inc/footer.php"); ?>