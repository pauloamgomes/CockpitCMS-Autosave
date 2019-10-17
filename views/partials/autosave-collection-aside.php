@render('autosave:views/partials/infoblock.php')
@render('autosave:views/partials/restore.php', ['type' => 'collection'])

<script>
  var $this = this;
  this.isReady = false;
  this.isUpdating = false;
  this.autosaved = false;
  this.autosavetime = null;

  this.on('mount', function() {
    if (this.entry._id) {
      $this.get();
    }
    this._entry = JSON.parse(JSON.stringify(this.entry));
    $this.isReady = true;
  });

  this.on('bindingupdated', function(data) {
    if (this.isReady && this.entry['_id'] && !this.isUpdating && !_.isEqual(this.entry, this._entry)) {
      window.setTimeout(this.autosave, 2000);
      $this.isUpdating = true;
      $this.update();
    }
  });

  this.get = function() {
    App.callmodule('autosave:get', this.entry._id, 'access').then(function(data) {
      $this.autosaved = data.result || null;
      $this.isUpdating = false;
      $this.update();
    }).catch(function(e){
      App.ui.notify('Error during autosave retrieve operation', 'danger');
    });
  };

  this.autosave = function() {
    App.callmodule('autosave:save',  {id: $this.entry._id, data: $this.entry }, 'access').then(function(data) {
      $this.autosavetime = data.result.updated || null;
      $this._entry = JSON.parse(JSON.stringify($this.entry));
      $this.isUpdating = false;
      $this.update();
    }).catch(function(e){
      App.ui.notify('Error during autosave save operation', 'danger');
    });
  }

</script>
