<!--  -->
<div class="site-index" style="text-align:center;">

<div class="row">
  <!-- Left side logo (col-md-2) -->
  <div class="col-md-2" style="background: red; height: 157px; color: white; text-align: justify; margin-top: -69px; display: flex; flex-direction: column; justify-content: flex-end;">
  <h3>Handysolver</h3>
</div>


  <!-- Right side content (col-md-10) -->
  <div class="col-md-10" style="text-align: center;
    margin-top: -37px;">
    <!-- Add your content here (e.g., navigation, text, etc.) -->
    <h3>To-do List Application</h3>
    <p>Where to-do items are added/deleted and belong to categories</p>
  </div>
</div>

    <div class="body-content">
        <p id="insert_success"></p>
    <form id="todo-form" action="<?= \yii\helpers\Url::to(['site/create']) ?>" method="post">
    <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>"> <!-- CSRF protection -->

    <div class="row" style="margin-left: 15%;">
        <div class="col-md-3 mb-3 field-loginform-password required">
            <select name="category" id="category" class="form-control">
                <option value="">Select Category</option>
                <?php if (!empty($category)): ?>
                    <?php foreach ($category as $cat): ?>
                        <option value="<?= htmlspecialchars($cat->id) ?>"><?= htmlspecialchars($cat->name) ?></option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">No categories found.</option>
                <?php endif; ?>
            </select>
        </div>
        <div class="col-md-6 mb-3 field-loginform-username required">
            <input type="text" id="item" name="item" class="col-lg-3 form-control" placeholder="Type todo item name" autofocus aria-invalid="false">
        </div>
        <div class="col-md-3 form-group">
            <div>
                <button type="submit" id="add-todo-btn" class="btn btn-primary" name="add-button" style="margin-right:100%;">Add</button>
            </div>
        </div>
    </div>
</form>

    </div>
<br><br>
    <div class="row" style="    border-top: 1px solid;">

    <table class="table table-hover">
  <thead>
    <tr>
        <!-- <th>s.no</th> -->
      <th>Todo item Name</th>
      <th>Category</th>
      <th>Timestamp</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody id="todo-items">
  <?php foreach ($todoItems as $index => $item): ?>
  <tr>
    <!-- <th scope="row"><?= $index + 1 ?></th> -->
    <td><?= htmlspecialchars($item['item']) ?></td> <!-- Access 'name' as an array key -->
    <td><?= htmlspecialchars($item['category_name'] ?? 'No Category') ?></td> <!-- Use 'category_name' as an array key and handle cases where it might be null -->
    <td>
    <?php
    try {
        // Create a new DateTime object from the timestamp
        $date = new DateTime($item['timestamp']);
        
        // Format the date as '6th Nov 2024'
        echo $date->format('jS M Y');
    } catch (Exception $e) {
        // In case of an error (e.g., invalid date format), show a fallback message
        echo 'Invalid Date';
    }
    ?>
</td>

    <td>
    <!-- Add class 'btn-delete' for the delete button -->
    <a href="<?= \yii\helpers\Url::to(['site/delete', 'id' => $item['id']]) ?>" class="btn btn-danger btn-delete" data-id="<?= $item['id'] ?>">Delete</a>
</td>

 

  </tr>
<?php endforeach; ?>

  </tbody>
</table>

    </div>
</div>
<!-- Include Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!-- Include jQuery (if not already included) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Include Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
    // Handle form submission
    $('#todo-form').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission
         var cat_id = $('#category').val()
         if(cat_id==''){
            toastr.error('Please Select category!'); 
            return false;

         }
         var item = $('#item').val()
         if(item==''){
            toastr.error('Please Fill Todo Item!'); 
            return false;

         }

        $.ajax({
            url: '<?= \yii\helpers\Url::to(['site/create']) ?>', // This will run the 'create' action
            type: 'POST',
            data: $(this).serialize(), // Serialize form data
            success: function(response) {
                if (response.success) {
                    // Display success message
                    toastr.success(response.message); // Show success toast
                    $('#todo-form')[0].reset(); // Reset the form
                    
                    // After successful creation, fetch updated Todo items
                    fetchUpdatedTodoItems();
                } else {
                    // Display validation errors
                    let errorMessage = 'Failed to add todo item:\n';
                    $.each(response.errors, function(key, errors) {
                        errorMessage += key + ': ' + errors.join(', ') + '\n'; // Join multiple errors
                    });
                    alert(errorMessage); // Display error message
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred: ' + error); // Display error if AJAX fails
            }
        });
    });

    // Function to fetch updated Todo items
    function fetchUpdatedTodoItems() {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['site/home2']) ?>', // This will run the 'home2' action
            type: 'GET', // Use GET because you just want to fetch the updated list
            dataType: 'json',
            success: function(response) {
                console.log(response);
                if (response.status === 'success') {
                    // Clear the existing rows
                    $('#todo-items').empty();

                    // Add new rows to the table with updated data
                    $.each(response.data, function(index, item) {
                        const newRow = `
                            <tr data-id="${item.id}">
                                <td>${item.item}</td>
                                <td>${item.category_name}</td>
                                <td>${item.timestamp}</td>
                                <td>
                                   <a href="<?= \yii\helpers\Url::to(['site/delete']) ?>?id=${item.id}" class="btn btn-danger btn-delete" data-id="${item.id}">Delete</a>
                                </td>
                            </tr>`;
                        // $('#todo-items').append(newRow);
                        $('#todo-items').prepend(newRow);
                    });
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Error occurred while fetching Todo items.');
            }
        });
    }
});

// delete
$(document).ready(function() {
 
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault(); 
        const btn = $(this); 
        const itemId = btn.data('id'); 

        if (confirm('Are you sure you want to delete this item?')) {
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['site/delete']) ?>', 
                type: 'GET',
                data: { id: itemId },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message); 
                        btn.closest('tr').remove();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('An error occurred while deleting the Todo item.');
                }
            });
        }
    });
});




</script>