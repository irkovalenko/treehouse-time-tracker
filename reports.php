<?php
require 'inc/functions.php';

$page = "reports";
$pageTitle = "Reports | Time Tracker";
$filter = '';

if (!empty($_GET['filter'])) {
    $filter = $_GET['filter'] ?? '';
}


include_once 'inc/header.php';
?>
<div class="col-container page-container">
    <div class="col col-70-md col-60-lg col-center">
        <div class="col-container">
            <h1 class='actions-header'>Reports</h1>
            <form class='form-container form-report' action='reports.php' method = 'get'>
                <label for='filter'>Filter:</label>
                <select id='filter' name='filter'>
                    <option value=''>Select one</option>
                    <optgroup label='Projects'></optgroup>
                    <?php
                    foreach (getProjectList() as $project) {
                        echo "<option value='{$project['project_id']}'>";
                        echo $project['title']. "</option>";
                    }
                    ?>
                    <optgroup label='Categories'></optgroup>
                    <option value="category:Billable">Billable</option>
                    <option value="category:Charity">Charity</option>
                    <option value="category:Personal">Personal</option>
                </select>
                <input class='button' type='submit' value='Apply' />

            </form>
        </div>
        <div class="section page">
            <div class="wrapper">
                <table>
                    <?php
                    $total = 0;
                    $projectTitle = '';
                    $projectTotal = 0;
            
                    foreach (getTasksList($filter) as $task) {
                        if ($projectTitle !== $task['project_title']) {
                            if ($projectTitle !== '') {
                                echo "<tr>\n";
                                echo "<td class='project-total-label' colspan='2'>Project Total</td>\n";
                                echo "<td class='project-total-number'>$projectTotal minutes</td>\n";
                                echo "</tr>\n";
                                $projectTotal = 0;
                            }
                            $projectTitle = $task['project_title'];
                            echo "<thead>\n";
                            echo "<tr>\n";
                            echo "<th colspan='3'>" . htmlspecialchars($task['project_title']) . "</th>\n";
                            echo "</tr>\n";
                            echo "<tr>\n";
                            echo "<th>Task</th>\n";
                            echo "<th>Date</th>\n";
                            echo "<th>Time</th>\n";
                            echo "</tr>\n";
                            echo "</thead>\n";
                        }
                        $projectTotal += $task['time'];
                        $total += $task['time'];
                        echo "<tr>\n";
                        echo "<td>" . htmlspecialchars($task['task_title']) . "</td>\n";
                        echo "<td>" . htmlspecialchars($task['date']) . "</td>\n";
                        echo "<td>" . $task['time'] . " minutes</td>\n";
                        echo "</tr>\n";
                    }

                    if ($projectTitle !== '') {
                        echo "<tr>\n";
                        echo "<td class='project-total-label' colspan='2'>Project Total</td>\n";
                        echo "<td class='project-total-number'>$projectTotal minutes</td>\n";
                        echo "</tr>\n";
                    }

                    ?>
                    <tr>
                        <th class='grand-total-label' colspan='2'>Grand Total</th>
                        <th class='grand-total-number'><?php echo $total; ?></th>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include "inc/footer.php"; ?>

