<?php ob_start(); ?>
<style>
    footer {
        height: 90px; 
    }
</style>
<footer class="fixed-bottom bg-light p-3">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <p> Brett Reader - A00986127 </p>
                <p>Kyoungran (Ran) Park - A01331544</p>
            </div>
            <div class="col-md-6 text-right">
                <button class="btn btn-danger" onclick="window.location.href='logout.php'">Logout</button>
            </div>
        </div>
    </div>
</footer>
<?php ob_end_flush(); ?>