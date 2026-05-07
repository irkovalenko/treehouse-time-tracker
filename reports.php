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
            <h1 class='actions-header'>Report on
                <?php
                if (empty($filter)) {
                    echo "all tasks by project";
                } else {
                     $filterParts = explode(':', $filter, 2);
                     $filterType = $filterParts[0];
                     $filterValue = $filterParts[1] ?? null;
                    echo htmlspecialchars($filterType) . " : ";
        
        switch ($filterType) {
            case 'project':
                $project = getProject($filterValue);
                echo htmlspecialchars($project['title']);
                break;
            case 'category':
                echo htmlspecialchars($filterValue);
                break;
            case 'date':
                $dateRange = null;
                if ($filterValue === 'today') {
                    $today = date('Y-m-d');
                    $dateRange = $today . " to " . $today;
                } elseif ($filterValue === 'week') {
                    $startOfWeek = date('Y-m-d', strtotime('monday this week'));
                    $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
                    $dateRange = $startOfWeek . " to " . $endOfWeek;
                } elseif ($filterValue === 'month') {
                    $startOfMonth = date('Y-m-d', strtotime('first day of this month'));
                    $endOfMonth = date('Y-m-d', strtotime('last day of this month'));
                    $dateRange = $startOfMonth . " to " . $endOfMonth;
                }
                echo htmlspecialchars($dateRange ?? $filterValue);
                break;
            default:
                echo htmlspecialchars($filter);
                break;
        }
    }
                     
                ?>
            </h1>
            <form class='form-container form-report' action='reports.php' method = 'get'>
                <label for='filter'>Filter:</label>
                <select id='filter' name='filter'>
                    <option value=''>Select one</option>
                    <optgroup label='Projects'></optgroup>
                    <?php
                    foreach (getProjectList() as $project) {
                        echo "<option value='project:{$project['project_id']}'>";
                        echo $project['title']. "</option>";
                    }
                    ?>
                    <optgroup label='Categories'></optgroup>
                    <option value="category:Billable">Billable</option>
                    <option value="category:Charity">Charity</option>
                    <option value="category:Personal">Personal</option>

                    <optgroup label='Dates'></optgroup>
                    <option value="date:today">Today</option>
                    <option value="date:week">This Week</option>
                    <option value="date:month">This Month</option>
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

