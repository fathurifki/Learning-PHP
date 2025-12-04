<?php 
  session_start();

  if (!isset($_SESSION['todos'])) {
    $_SESSION['todos'] = [];
  }

  if (!isset($_SESSION['editing_todo_id'])) {
    $_SESSION['editing_todo_id'] = null;
  }

  function console_log($output, $with_script_tags = true) {
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . ');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
  }
  
  
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['add_todo']) && !empty($_POST['todo_text'])) {
      $newTodo = [
       'id' => uniqid(),
      'text' => htmlspecialchars(trim($_POST['todo_text']), ENT_QUOTES, 'UTF-8'),
       'completed' => false,
       'created_at' => date('Y-m-d H:i:s'),
      ];
      $_SESSION['todos'][] = $newTodo;
      $_SESSION['editing_todo_id'] = null;
    }


    if (isset($_POST['toggle_todo'])) {
      $todoId = $_POST['todo_id'];
      foreach ($_SESSION['todos'] as &$todo) {
        if ($todo['id'] === $todoId) {
          $todo['completed'] = !$todo['completed'];
          break;
        }
      }
      unset($todo);
      $_SESSION['editing_todo_id'] = null;
    }

    if(isset($_POST['edit_todo'])) {
      $todoId = $_POST['todo_id'];
      $_SESSION['editing_todo_id'] = $todoId;
    }

    if(isset($_POST['save_edit'])) {
      $todoId = $_POST['todo_id'];
      $todoEditText = $_POST['todo_text'];
      foreach ($_SESSION['todos'] as &$todo) {
        if ($todoId === $todo['id']) {
          $todo['text'] = $todoEditText;
          break;
        }
      }
      unset($todo);
      $_SESSION['editing_todo_id'] = null;
   }

   if(isset($_POST['cancel_edit'])) {
    $_SESSION['editing_todo_id'] = null;
  }

    if(isset($_POST['delete_todo'])) {
      $todoId = $_POST['todo_id'];
      $_SESSION['todos'] = array_filter($_SESSION['todos'], function($todo) use ($todoId) {
        return $todo['id'] !== $todoId;
      });
      $_SESSION['todos'] = array_values($_SESSION['todos']);
      $_SESSION['editing_todo_id'] = null;
    }


    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
  }

  console_log($_SESSION['todos']);
  $todos = $_SESSION['todos'];
  $editingTodoId = $_SESSION['editing_todo_id'];
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>PHP + Docker Starter</title>
    <style>
      body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; margin: 40px; }
      code { background: #f5f5f5; padding: 2px 6px; border-radius: 4px; }
    </style>
  </head>
  <body>
    <h1>It works! 🎉</h1>
    <p>This project runs <code>php:8.2-apache</code> via Docker Compose.</p>
    <ul>
      <li>PHP version: <strong><?php echo htmlspecialchars($phpVersion, ENT_QUOTES, 'UTF-8'); ?></strong></li>
      <li>Document root: <code>/var/www/html</code></li>
      <li>Host port: <code>http://localhost:8080</code></li>
    </ul>

    <?php $title = "My Page"; $items = ["Item 1", "Item 2", "Item 3"]?>
    <h1><?php echo $title; ?></h1>
    <ul>
        <?php foreach ($items as $item): ?>
          <li><?php echo $item; ?></li>
        <?php endforeach; ?>
    </ul>
    <p>Edit <code>src/index.php</code> and refresh the page to see changes.</p>
    <h1>Todos PHP</h1>
    <form method="POST" class="todo-form">
      <input placeholder="Input Todos Here" name="todo_text"/>
      <button name="add_todo" type="submit">Submit</button>
    </form> 
    <?php if(empty($todos)): ?>
      <span>There is no todos Here</span>
    <?php else: ?>
      <?php foreach ($todos as $todo): ?>
      <div style="display: flex; gap: 10px;">
      <?php if ($editingTodoId === $todo['id']): ?>
        <form method="POST">
        <input type="hidden" name="todo_id" value="<?php echo htmlspecialchars($todo['id']); ?>" />
        <input 
          type="text" 
          name="todo_text"
          value="<?php echo htmlspecialchars($todo['text']); ?>"
          autofocus
          required
        />
        <button type="submit" name="save_edit" class="btn btn-save">Save</button>
        <button type="submit" name="cancel_edit" class="btn btn-cancel">Cancel</button>
        </form>
      <?php else: ?>
        <form method="POST">
        <input type="hidden" name="todo_id" value="<?php echo htmlspecialchars($todo['id']); ?>" />
        <input 
          type="checkbox" 
          class="todo-checkbox"
          <?php echo $todo['completed'] ? 'checked' : ''; ?>
          onchange="this.form.submit()"
        />
        <input type="hidden" name="toggle_todo" value="1" />
      </form>
      <?php endif; ?>
      <span class="todo-text"><?php echo htmlspecialchars($todo['text']); ?></span>
        <form method="POST">
          <input type="hidden" name="todo_id" value="<?php echo htmlspecialchars($todo['id']); ?>"/>
          <button type="submit" name="delete_todo">DELETE</button>
          <button type="submit" name="edit_todo">EDIT</button>
        </form>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>  
  </body>
</html>
