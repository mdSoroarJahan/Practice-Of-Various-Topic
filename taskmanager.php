<?php

// Task Manager using JSON File Storage

const FILE_PATH = 'tasks.json';

function loadTasks() {
    return file_exists(FILE_PATH) ? json_decode(file_get_contents(FILE_PATH), true) : [];
}

function saveTasks($tasks) {
    file_put_contents(FILE_PATH, json_encode($tasks, JSON_PRETTY_PRINT));
}

function addTask($title, $description) {
    $tasks = loadTasks();
    $tasks[] = ['id' => uniqid(), 'title' => $title, 'description' => $description, 'completed' => false];
    saveTasks($tasks);
    echo "Task added successfully!\n";
}

function listTasks() {
    $tasks = loadTasks();
    if (empty($tasks)) {
        echo "No tasks found.\n";
        return;
    }
    foreach ($tasks as $task) {
        echo "[{$task['id']}] {$task['title']} - {$task['description']} (" . ($task['completed'] ? 'Completed' : 'Pending') . ")\n";
    }
}

function updateTask($id, $title, $description) {
    $tasks = loadTasks();
    foreach ($tasks as &$task) {
        if ($task['id'] === $id) {
            $task['title'] = $title;
            $task['description'] = $description;
            saveTasks($tasks);
            echo "Task updated successfully!\n";
            return;
        }
    }
    echo "Task not found.\n";
}

function deleteTask($id) {
    $tasks = loadTasks();
    $tasks = array_filter($tasks, fn($task) => $task['id'] !== $id);
    saveTasks(array_values($tasks));
    echo "Task deleted successfully!\n";
}

function markCompleted($id) {
    $tasks = loadTasks();
    foreach ($tasks as &$task) {
        if ($task['id'] === $id) {
            $task['completed'] = true;
            saveTasks($tasks);
            echo "Task marked as completed!\n";
            return;
        }
    }
    echo "Task not found.\n";
}

if ($argc < 2) {
    echo "Usage: php script.php [add|list|update|delete|complete] [parameters]\n";
    exit(1);
}

$command = $argv[1];

switch ($command) {
    case 'add':
        if ($argc < 4) {
            echo "Usage: php script.php add <title> <description>\n";
            exit(1);
        }
        addTask($argv[2], $argv[3]);
        break;
    case 'list':
        listTasks();
        break;
    case 'update':
        if ($argc < 5) {
            echo "Usage: php script.php update <id> <title> <description>\n";
            exit(1);
        }
        updateTask($argv[2], $argv[3], $argv[4]);
        break;
    case 'delete':
        if ($argc < 3) {
            echo "Usage: php script.php delete <id>\n";
            exit(1);
        }
        deleteTask($argv[2]);
        break;
    case 'complete':
        if ($argc < 3) {
            echo "Usage: php script.php complete <id>\n";
            exit(1);
        }
        markCompleted($argv[2]);
        break;
    default:
        echo "Invalid command. Use add, list, update, delete, or complete.\n";
}