<?php include 'app/views/shares/header.php'; ?>
<h1>Danh sách sản phẩm</h1>
<a href="<?php echo $base_url; ?>/Product/add" class="btn btn-success mb-2">Thêm sản phẩm mới</a>
<ul class="list-group" id="product-list">
<!-- Danh sách sản phẩm sẽ được tải từ API và hiển thị tại đây -->
</ul>
<?php include 'app/views/shares/footer.php'; ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
const basePath = '<?php echo $base_url; ?>';
const token = localStorage.getItem('jwtToken');
if (!token) {
alert('Vui lòng đăng nhập');
location.href = basePath + '/account/login'; // Điều hướng đến trang đăng nhập
return;
}
fetch(basePath + '/api/product', {
method: 'GET',
headers: {
'Content-Type': 'application/json',
'Authorization': 'Bearer ' + token
}
})
.then(response => response.json())
.then(data => {
const productList = document.getElementById('product-list');
// Hỗ trợ cả trường hợp API trả về mảng trực tiếp hoặc bọc trong thuộc tính data
const products = data.data || data;
if (Array.isArray(products)) {
products.forEach(product => {
const productItem = document.createElement('li');
productItem.className = 'list-group-item';
productItem.innerHTML = `
<h2><a href="${basePath}/Product/show/${product.id}">${product.name}</a></h2>
<p>${product.description}</p>
<p>Giá: ${product.price} VND</p>
<p>Danh mục: ${product.category_name}</p>
<a href="${basePath}/Product/edit/${product.id}" class="btn btn-warning">Sửa</a>
<button class="btn btn-danger" onclick="deleteProduct(${product.id})">Xóa</button>
`;
productList.appendChild(productItem);
});
} else {
productList.innerHTML = `<li class="list-group-item text-danger">Không thể tải dữ liệu sản phẩm.</li>`;
}
});
});

function deleteProduct(id) {
if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
const token = localStorage.getItem('jwtToken');
const basePath = '<?php echo $base_url; ?>';
fetch(basePath + `/api/product/${id}`, {
method: 'DELETE',
headers: {
'Authorization': 'Bearer ' + token
}
})
.then(response => response.json())
.then(data => {
if (data.success || data.message === 'Product deleted successfully' || data.message === 'Xóa sản phẩm thành công.') {
location.reload();
} else {
alert('Xóa sản phẩm thất bại: ' + (data.message || ''));
}
});
}
}
</script>