<?php include_once 'app/views/shares/header.php'; ?>

<div class="container my-5">
    <h2 class="fw-bold mb-4">📦 Đơn hàng của tôi</h2>
    
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td class="ps-4">#<?php echo $order->id; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order->created_at)); ?></td>
                            <td><?php echo number_format($order->total_price, 0, ',', '.'); ?>đ</td>
                            <td>
                                <?php 
                                    $badge = 'bg-secondary';
                                    if ($order->status == 'Chờ xử lý') $badge = 'bg-warning text-dark';
                                    elseif ($order->status == 'Đang giao') $badge = 'bg-info text-dark';
                                    elseif ($order->status == 'Đã giao') $badge = 'bg-success';
                                    elseif ($order->status == 'Yêu cầu trả hàng') $badge = 'bg-dark text-white';
                                    elseif ($order->status == 'Đã trả hàng') $badge = 'bg-secondary';
                                    elseif ($order->status == 'Đã hủy') $badge = 'bg-danger';
                                ?>
                                <span class="badge <?php echo $badge; ?>"><?php echo $order->status; ?></span>
                            </td>
                            <td class="text-end pe-4">
                                <?php if ($order->status == 'Chờ xử lý'): ?>
                                    <a href="/HuynhVanGiang-4733/Order/cancelOrder/<?php echo $order->id; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?')">
                                       Hủy đơn
                                    </a>
                                <?php elseif ($order->status == 'Đang giao'): ?>
                                    <a href="/HuynhVanGiang-4733/Order/confirmDelivery/<?php echo $order->id; ?>" 
                                       class="btn btn-sm btn-success"
                                       onclick="return confirm('Xác nhận bạn đã nhận được hàng?')">
                                       Đã nhận hàng
                                    </a>
                                <?php elseif ($order->status == 'Đã giao'): ?>
                                    <a href="/HuynhVanGiang-4733/Order/requestReturn/<?php echo $order->id; ?>" 
                                       class="btn btn-sm btn-warning"
                                       onclick="return confirm('Bạn có chắc chắn muốn yêu cầu trả hàng cho đơn này không?')">
                                       Trả hàng
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">Bạn chưa có đơn hàng nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once 'app/views/shares/footer.php'; ?>