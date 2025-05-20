<style>
#global-loader {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(255, 255, 255, 0.85);
  z-index: 9999;
  display: none;
  align-items: center;
  justify-content: center;
}

.loader-inner {
  text-align: center;
  position: absolute;
  top: 50%;
  left: 50%;
}
</style>

<div id="global-loader">
  <div class="loader-inner">
    <div class="spinner-border text-primary" role="status"></div>
    <div>Processing...</div>
  </div>
</div>


