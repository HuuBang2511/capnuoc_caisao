<script>
    $(function() {
        var hasSuccess = <?= Yii::$app->session->hasFlash('success') ? 'true' : 'false' ?>,
            hasError = <?= Yii::$app->session->hasFlash('error') ? 'true' : 'false' ?>,
            hasWarning = <?= Yii::$app->session->hasFlash('warning') ? 'true' : 'false' ?>;
        
        if(hasSuccess) {
            var success = '<?= Yii::$app->session->getFlash('success') ?>';
            toastMessage('success', success)
        } 

        if(hasError) {
            var error = '<?= Yii::$app->session->getFlash('error') ?>';
            toastMessage('error', error)
        } 

        if(hasError) {
            var warning = '<?= Yii::$app->session->getFlash('warning') ?>';
            toastMessage('warning', warning)
        } 
    })
</script>