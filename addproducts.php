<?php
include 'admin_auth.php'; 
// Connect to database
$conn = new mysqli("localhost", "root", "", "jewelry_database");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



// Fetch existing categories
$categories = [];
$result = $conn->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != ''");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $category = $_POST["category"];
    
    // Handle new category
    if ($category === "other" && !empty($_POST["new_category"])) {
        $category = $_POST["new_category"];
    }

    // Determine if this is a ring product
    $isRing = (strtolower($category) === 'rings' || strtolower($category) === 'ring');
    
    // For non-ring items, use regular stock
    $stock = !$isRing && isset($_POST["stock"]) ? $_POST["stock"] : 0;

    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert product
        $sql = "INSERT INTO products (name, description, price, category, stock) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsi", $name, $description, $price, $category, $stock);
        
        if (!$stmt->execute()) {
            throw new Exception("Error inserting product: " . $stmt->error);
        }
        
        $product_id = $stmt->insert_id;
        $stmt->close();

        // Handle ring sizes and inventory
        if ($isRing && isset($_POST['ring_sizes']) && isset($_POST['ring_stocks'])) {
            $sizes = $_POST['ring_sizes'];
            $stocks = $_POST['ring_stocks'];
            
            $stmt_inv = $conn->prepare("INSERT INTO product_inventory (product_id, size, stock) VALUES (?, ?, ?)");
            
            foreach ($sizes as $index => $size) {
                $size = trim($size);
                $stockQty = isset($stocks[$index]) ? intval($stocks[$index]) : 0;
                
                if (!empty($size)) {
                    $stmt_inv->bind_param("isi", $product_id, $size, $stockQty);
                    $stmt_inv->execute();
                }
            }
            $stmt_inv->close();
        }

        // Handle image uploads
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $firstImagePath = null;

        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if (empty($_FILES['images']['name'][$key])) continue;
                if ($_FILES['images']['error'][$key] !== UPLOAD_ERR_OK) continue;

                $imageName = time() . "_" . $key . "_" . basename($_FILES['images']['name'][$key]);
                $targetFile = $targetDir . $imageName;

                if (move_uploaded_file($tmp_name, $targetFile)) {
                    $stmt_img = $conn->prepare("INSERT INTO product_images (product_id, image_path, uploaded_at) VALUES (?, ?, NOW())");
                    $stmt_img->bind_param("is", $product_id, $targetFile);
                    $stmt_img->execute();
                    $stmt_img->close();

                    if ($firstImagePath === null) {
                        $firstImagePath = $targetFile;
                    }
                }
            }

            if ($firstImagePath !== null) {
                $stmt_main = $conn->prepare("UPDATE products SET image=? WHERE id=?");
                $stmt_main->bind_param("si", $firstImagePath, $product_id);
                $stmt_main->execute();
                $stmt_main->close();
            }
        }

        // Commit transaction
        $conn->commit();
        
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
        exit;
        
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        $message = "❌ Error: " . $e->getMessage();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Product - Admin</title> 
<script src="script.js" defer></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body {
    background: #f7f7f7;
}
h2 {
    text-align: center;
    font-family: "Abril Fatface", serif;
    font-style: italic;
    font-size: 25px;
    font-weight: 600;
    margin-top: 30px;
    margin-bottom: 30px;
}
form {
    background: #fff;
    padding: 2.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    width: 600px;
    margin: auto;
}
input, textarea, select {
    width: 100%;
    padding: 0.7rem;
    margin-bottom: 1rem;
    border-radius: 10px;
    resize: none;
    border: 1px dashed #ccc;
}
button {
    display: block;
    width: 100%;
    padding: 0.8rem;
    border-radius: 2rem;
    background-color: #111;
    color: #fff;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: all 0.3s ease;
}
button:hover {
    background-color: #fff;
    color: #111;
    border: 1px solid #111;
}
.message {
    text-align: center;
    color: green;
    font-weight: bold;
    margin-bottom: 1rem;
}
.preview-container {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 1rem;
    justify-content: center;
}
.preview-item {
    position: relative;
    width: 80px;
    height: 80px;
    margin-bottom: 30px;
}
.preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border: 2px solid #ccc;
    border-radius: 6px;
    transition: transform 0.2s ease;
}
.preview-item:hover img {
    transform: scale(1.05);
    border-color: #111;
}
.remove-btn {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 24px;
    height: 24px;
    background: #ff4444;
    color: white;
    border: 2px solid white;
    border-radius: 50%;
    cursor: pointer;
    display: none;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    transition: all 0.2s ease;
}
.preview-item:hover .remove-btn {
    display: flex;
}
.remove-btn:hover {
    background: #cc0000;
    transform: scale(1.1);
    color: white;
}
#newCategoryField {
    display: none;
}
.upload-info {
    font-size: 12px;
    color: #666;
    margin-top: -8px;
    margin-bottom: 1rem;
}

/* Ring Size Inventory Styles */
#ringSizeInventorySection {
    display: none;
    border: 1px dashed #ccc;
    padding: 1.5rem;
    border-radius: 10px;
    margin-bottom: 1rem;
    background: #f9f9f9;
}

.size-inventory-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.add-size-btn {
    padding: 0.5rem 1rem;
    background:rgb(22, 23, 22);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.2s;
}

.add-size-btn:hover {
    background:rgb(230, 234, 230);
    color:rgb(22, 23, 22)
}

.size-row {
    display: grid;
    grid-template-columns: 1fr 1fr 40px;
    gap: 30px;
    margin-bottom: 0.8rem;
    align-items: end;
}

.size-row input {
    margin-bottom: 0;
}

.remove-size-btn {
    width: 35px;
    height: 35px;
    background: #ff4444;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    transition: all 0.2s;
    padding: 0;
}

.remove-size-btn:hover {
    background:rgb(243, 66, 66);
    transform: scale(1.05);
    color: white;
}

#regularStockField {
    display: block;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
  }
  
  body {
    background-color: #fff;
    color: #111;
    overflow-x: hidden;
  }
  
 
  
  /*MAIN NAVBAR*/
  .main-navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 3rem;
    background-color: #fff;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
  }
  
  .logo {
    font-family: "Abril Fatface", serif;
    font-size: 2rem;
    font-style: italic;
    font-weight: 500;
    text-decoration: none;
    color: #111;
  }
  
  .nav-icons {
    display: flex;
    align-items: center;
    gap: 25px;
    font-size: 19px;
  }
  
  .nav-icons i {
    cursor: pointer !important;
    transition: opacity 0.3s ease;
 }

  
  .nav-icons i:hover {
    color: rgb(225, 203, 75);
    opacity: 0.6;
  }
  
  /* LOWER NAV LINKS */
  .nav-links {
    display: flex;
    justify-content: center;
    gap: 2.5rem;
    padding: 0.8rem 0;
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
    background-color: #fff;
  }
  
  .nav-links a {
    text-decoration: none;
    color: #111;
    font-size: 0.9rem;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    position: relative;
  }
  
  .nav-links a:hover{
    text-decoration: underline;
  }
  /* ===== HAMBURGER ===== */
.hamburger {
    display: none;
    flex-direction: column;
    cursor: pointer;
  }
  
  .hamburger div {
    width: 25px;
    height: 3px;
    background-color: #111;
    margin: 4px 0;
    transition: all 0.3s ease;
  }
  
  .hamburger.open div:nth-child(1) {
    transform: rotate(45deg) translate(5px, 5px);
  }
  
  .hamburger.open div:nth-child(2) {
    opacity: 0;
  }
  
  .hamburger.open div:nth-child(3) {
    transform: rotate(-45deg) translate(5px, -5px);
  }
  
@media (max-width: 900px) {
    .main-navbar {
      justify-content: space-between;
      padding: 1rem;
    }
  
    .logo {
      position: absolute;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
    }
  
    .hamburger {
      display: flex;
    }
  
    .nav-icons {
      display: flex;
    }
  
    .nav-links {
      display: none;
      flex-direction: column;
      position: absolute;
      top: 60px; 
      left: 10px;
      width: 100%;
      background: #fff;
      padding: 10px;
      gap: 1rem;
      z-index: 999;
      text-decoration: none;
      margin-left: -10px;
     
    }
  
    .nav-links.active {
      display: flex;
      position: fixed;
    }

    .nav-links a:hover::after {
        text-decoration: none !important;
    }
    
  
    .hamburger.open div:nth-child(1) {
      transform: rotate(45deg) translate(6px, 8px);
    }
    .hamburger.open div:nth-child(2) {
      opacity: 0;
    }
    .hamburger.open div:nth-child(3) {
      transform: rotate(-45deg) translate(6px, -10px);
    } 


    }
</style>
</head>
<body>

<!-- Main Navbar -->
<nav class="main-navbar">

<div class="logo"><a class="logo" href="index.php">Âme</a></div>

<!-- Hamburger Menu -->
<div class="hamburger" id="hamburger">
    <div></div>
    <div></div>
    <div></div>
</div>

</nav>

<!-- Lower Navigation Links -->
<div class="nav-links" id="navLinks">
    <a href="admin.php">Dashboard</a>
    <a href="addproducts.php">Add Products</a>
    <a href="discounts.php">Discounts</a>
    <a href="admin_orders.php">Orders</a>
    <a href="analytics.php">Analytics</a>
    <a href="inventory.php">Inventory</a>
</div>

<h2>Add New Product</h2>

<?php if (isset($_GET['success'])): ?>
<div class="message">✅ Product added successfully!</div>
<?php endif; ?>

<?php if (!empty($message)): ?>
<div class="message" style="color: red;"><?= $message ?></div>
<?php endif; ?>

<div class="product-form">

<form method="POST" enctype="multipart/form-data">
    <label>Product Name:</label>
    <input type="text" name="name" required>

    <label>Description:</label>
    <textarea name="description" rows="4" required></textarea>

    <label>Price:</label>
    <input type="number" step="0.01" name="price" required>

    <label>Category:</label>
    <select name="category" id="categorySelect" required>
        <option value="">Select a category</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
        <?php endforeach; ?>
        <option value="other">➕ Add New Category</option>
    </select>

    <div id="newCategoryField">
        <label>New Category Name:</label>
        <input type="text" name="new_category" placeholder="Enter new category">
    </div>

    <!-- Regular Stock Field (for non-ring items) -->
    <div id="regularStockField">
        <label>Inventory Stock:</label>
        <input type="number" name="stock" id="stockInput" min="0" value="0">
    </div>

    <!-- Ring Size Inventory Section -->
    <div id="ringSizeInventorySection">
        <div class="size-inventory-header">
            <button type="button" class="add-size-btn" onclick="addSizeRow()">
                <i class="fas fa-plus"></i> Add Size
            </button>
        </div>
        <div id="sizeRowsContainer">
            <!-- Size rows will be added here dynamically -->
        </div>
    </div>

    <label>Product Images:</label>
    <input type="file" id="fileInput" name="images[]" accept="image/*" multiple required>
    <div class="upload-info">Selected: <span id="fileCount">0</span> images</div>
    <div id="preview" class="preview-container"></div>

    <button type="submit">Add Product</button>
</form>

</div>

<script>
// Category handling
const categorySelect = document.getElementById("categorySelect");
const newCategoryField = document.getElementById("newCategoryField");
const regularStockField = document.getElementById("regularStockField");
const ringSizeInventorySection = document.getElementById("ringSizeInventorySection");
const sizeRowsContainer = document.getElementById("sizeRowsContainer");

function updateCategoryFields() {
    const value = categorySelect.value;
    const valueLower = value.toLowerCase();

    // Show new category input if "other"
    newCategoryField.style.display = value === "other" ? "block" : "none";

    // Show ring size inventory if "ring" or "rings"
    const isRing = valueLower === "ring" || valueLower === "rings";
    ringSizeInventorySection.style.display = isRing ? "block" : "none";
    regularStockField.style.display = isRing ? "none" : "block";

    // Add default size row if showing ring section and no rows exist
    if (isRing && sizeRowsContainer.children.length === 0) {
        addSizeRow();
    }
}

updateCategoryFields();
categorySelect.addEventListener("change", updateCategoryFields);

// Ring size management
let sizeRowCounter = 0;

function addSizeRow() {
    sizeRowCounter++;
    const rowDiv = document.createElement('div');
    rowDiv.className = 'size-row';
    rowDiv.id = 'size-row-' + sizeRowCounter;
    
    rowDiv.innerHTML = `
        <div>
            <input type="text" 
                   name="ring_sizes[]" 
                   placeholder="Size (e.g., 6, 7, 8)" 
                   required>
        </div>
        <div>
            <input type="number" 
                   name="ring_stocks[]" 
                   placeholder="Stock quantity" 
                   min="0" 
                   value="0"
                   required>
        </div>
        <button type="button" class="remove-size-btn" onclick="removeSizeRow(${sizeRowCounter})">
            <i class="fas fa-trash"></i>
        </button>
    `;
    
    sizeRowsContainer.appendChild(rowDiv);
}

function removeSizeRow(id) {
    const row = document.getElementById('size-row-' + id);
    if (row) {
        row.remove();
    }
    
    // Add at least one row if all removed
    if (sizeRowsContainer.children.length === 0) {
        addSizeRow();
    }
}

// Image preview functionality
const fileInput = document.getElementById('fileInput');
const preview = document.getElementById('preview');
const fileCount = document.getElementById('fileCount');

let selectedFiles = new DataTransfer();

fileInput.addEventListener('change', (e) => {
    const newFiles = Array.from(e.target.files);
    newFiles.forEach(file => {
        selectedFiles.items.add(file);
    });
    updatePreview();
});

function updatePreview() {
    preview.innerHTML = '';
    fileCount.textContent = selectedFiles.files.length;
    
    Array.from(selectedFiles.files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item';
            
            const img = document.createElement('img');
            img.src = e.target.result;
            img.alt = file.name;
            
            const removeBtn = document.createElement('button');
            removeBtn.className = 'remove-btn';
            removeBtn.innerHTML = '<i class="fas fa-trash"></i>';
            removeBtn.type = 'button';
            removeBtn.onclick = (event) => {
                event.preventDefault();
                removeImage(index);
            };
            
            previewItem.appendChild(img);
            previewItem.appendChild(removeBtn);
            preview.appendChild(previewItem);
        };
        reader.readAsDataURL(file);
    });
    
    fileInput.files = selectedFiles.files;
}

function removeImage(index) {
    const dt = new DataTransfer();
    Array.from(selectedFiles.files).forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });
    
    selectedFiles = dt;
    updatePreview();
}
</script>

</body>
</html>