<?php
$conn = new mysqli('localhost', 'root', '', 'tasks');

if($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['add_task']) && !empty($_POST['form_title']) && !empty($_POST['form_description'])) {
        $title = $_POST['form_title'];
        $description = $_POST['form_description'];
        $stmt = $conn->prepare("INSERT INTO tasks (title, description, status) VALUES (?, ?, '1')");
        $stmt->bind_param('ss', $title, $description);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['new_status']) && !empty($_POST['task_id'])) {
        $task_id = $_POST['task_id'];
        $new_status = $_POST['new_status'];
        $stmt = $conn->prepare("UPDATE tasks SET status=? WHERE id=?");
        $stmt->bind_param('si', $new_status, $task_id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['edit_task']) && !empty($_POST['task_id']) && !empty($_POST['new_title']) && !empty($_POST['new_description'])) {
        $task_id = $_POST['task_id'];
        $new_title = $_POST['new_title'];
        $new_description = $_POST['new_description'];
        error_log("Received task_id: $task_id");
        error_log("New title: $new_title");
        error_log("New description: $new_description");

        $stmt = $conn->prepare("UPDATE tasks SET title=?, description=? WHERE id=?");
        $stmt->bind_param('ssi', $new_title, $new_description, $task_id);
        if ($stmt->execute()) {
            error_log("Task updated successfully");
        } else {
            error_log("Error updating task: " . $stmt->error);
            echo "<script>alert('An error occurred while updating the task.');</script>";
        }
        $stmt->close();
    } elseif (isset($_POST['delete_task']) && !empty($_POST['task_id'])) {
        $task_id = $_POST['task_id'];
        $stmt = $conn->prepare("DELETE FROM tasks WHERE id=?");
        $stmt->bind_param('i', $task_id);
        if ($stmt->execute()) {
            error_log("Task deleted successfully");
        } else {
            error_log("Error deleting task: " . $stmt->error);
        }
        $stmt->close();
    }
}

$result = $conn->query('SELECT * FROM tasks');
?>

<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Gestionnaire de Tâches</title>
    <link rel='stylesheet' href='./style.css' />
    <script src='./script.js' defer></script>
</head>
<body>
    <h1 id='page-header'>Gestionnaire de tâches</h1>

    <article id='page'>

    <h2>Ajouter une tâche:</h2>

    <form method='post' id='task-container'>
        <input name='form_title' type='text' id='form_title' placeholder='Titre' required />
        <textarea name='form_description' id='form_description' placeholder='Description' required></textarea>
        <button type='submit' name='add_task'>Ajouter</button>
    </form>

    <h2>Liste des tâches:</h2>
    <table id='list-of-tasks'>
        <thead>
            <tr>
                <th>Titre</th>
                <th>Description</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td>
                    <span class='view-mode'><?php echo htmlspecialchars($row['title']); ?></span>
                    <input type='text' name='new_title' class='edit-mode' value='<?php echo htmlspecialchars($row['title']); ?>' style='display:none;'>
                </td>
                <td>
                    <span class='view-mode'><?php echo htmlspecialchars($row['description']); ?></span>
                    <textarea name='new_description' class='edit-mode' style='display:none;'><?php echo htmlspecialchars($row['description']); ?></textarea>
                </td>
                <td>
                    <form method='post'>
                        <input type='hidden' name='task_id' value='<?php echo $row['id']; ?>'>
                        <select name='new_status' onchange='this.form.submit()'>
                            <option value='1' <?php if ($row['status'] == '1') echo 'selected'; ?>>À faire</option>
                            <option value='2' <?php if ($row['status'] == '2') echo 'selected'; ?>>En cours</option>
                            <option value='3' <?php if ($row['status'] == '3') echo 'selected'; ?>>Terminée</option>
                        </select>
                    </form>
                </td>
                <td id='action-container'>
                    <button type='button' class='edit-button' onclick='toggleEdit(this)'>Modifier</button>
                    <form method='post' style='display:inline;'>
                        <input type='hidden' name='task_id' value='<?php echo $row['id']; ?>'>
                        <input type='hidden' name='new_title' value=''>
                        <input type='hidden' name='new_description' value=''>
                        <button type='submit' name='edit_task' class='save-button' style='display:none;' onclick='return prepareEdit(this);'>Enregistrer</button>
                    </form>
                    <form method='post' style='display:inline;'>
                        <input type='hidden' name='task_id' value='<?php echo $row['id']; ?>'>
                        <button type='submit' name='delete_task' onclick="return confirm('Êtes-vous sûr de vouloir effacer cette tâche?');">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    </article>

</body>
</html>