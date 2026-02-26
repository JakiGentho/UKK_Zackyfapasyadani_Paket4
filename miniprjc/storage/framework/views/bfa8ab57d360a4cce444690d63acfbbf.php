<?php $__env->startSection('index'); ?>
    <div class="container">
      <div class="heading_container heading_center">
        <h2>
          Latest Products
        </h2>
      </div>
      <div class="row">
        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-sm-6 col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="100">
          <div class="box">
            <a href="<?php echo e(route('product_details', $product->id)); ?>">
              <div class="img-box">
                <img src="<?php echo e(asset('products/'.$product->product_image)); ?>" alt="">
              </div>
              <div class="detail-box">
                <h6>
                  <?php echo e($product->product_title); ?>

                </h6>
                <h6>
                  Price
                  <span>
                    $<?php echo e($product->product_prices); ?>

                  </span>
                </h6>
              </div>
              <div class="new">
                <span>
                  New
                </span>
              </div>
            </a>
          </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <div class="btn-box" data-aos="zoom-in" data-aos-delay="50">
        <a href="<?php echo e(route('viewallproducts')); ?>">
          View All Products
        </a>
      </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.maindesign', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\miniprjc\resources\views/users/index.blade.php ENDPATH**/ ?>