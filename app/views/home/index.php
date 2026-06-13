<?php include_once 'app/views/shares/header.php'; ?>

<div class="container my-5 text-center">
    <h1 class="fw-bold text-dark mb-2" style="font-size: 3rem; letter-spacing: -0.05rem;">Khám phá dòng sản phẩm.</h1>
    <p class="text-muted fs-5">Những sản phẩm công nghệ tiên tiến nhất dành cho bạn.</p>
</div>

<div class="container mb-5">
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 justify-content-center">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $pro): ?>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-3 text-center position-relative" 
                         style="background-color: #f5f5f7; transition: transform 0.3s ease, box-shadow 0.3s ease;"
                         onmouseover="this.style.transform='減cale(1.02)'; this.style.boxShadow='0 1rem 3rem rgba(0,0,0,0.08)';"
                         onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 .125rem .25rem rgba(0,0,0,0.075)';">
                        


                        <div class="card-body d-flex flex-column p-2">
                            <h4 class="fw-bold text-dark mb-1" style="font-size: 1.35rem;"><?php echo htmlspecialchars($pro->name); ?></h4>
                            <p class="text-muted small flex-grow-1 text-truncate-2 mb-3" style="min-height: 40px;">
                                <?php echo htmlspecialchars($pro->description); ?>
                            </p>
                            
                            <div class="mb-3">
                                <span class="fw-bold text-danger fs-5">
                                    <?php echo number_format($pro->price, 0, ',', '.'); ?>đ
                                </span>
                            </div>

                            <div class="d-grid gap-2 mt-auto">
                                <a href="/HuynhVanGiang-4733/Cart/addToCart/<?php echo $pro->id; ?>" 
                                   class="btn btn-primary rounded-pill fw-medium py-2 shadow-sm" 
                                   style="background-color: #0071e3; border: none;">
                                    <i class="fa-solid fa-bag-shopping me-1"></i> Mua ngay
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center my-5">
                <p class="text-muted">Hiện tại cửa hàng đang cập nhật sản phẩm mới, bạn vui lòng quay lại sau nhé!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once 'app/views/shares/footer.php'; ?>