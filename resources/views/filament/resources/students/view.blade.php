<x-filament-panels::page>
    @if ($this->hasInfolist())
        {{ $this->infolist }}
        <div id="qrcode" style="width:100px; height:100px; margin-top:50px;"></div>
    @else
        {{ $this->form }}
    @endif
 
    @if (count($relationManagers = $this->getRelationManagers()))
        <x-filament-panels::resources.relation-managers
            :active-manager="$this->activeRelationManager"
            :managers="$relationManagers"
            :owner-record="$record"
            :page-class="static::class"
        />
    @endif
</x-filament-panels::page>
<script src="{{ asset('qrcode.js') }}"></script>
<script>
   var qrcode = new QRCode(document.getElementById("qrcode"), {
   //  text: "http://jindo.dev.naver.com/collie",
    width: 100,
    height: 100,
   //  colorDark : "#000000",
   //  colorLight : "#ffffff",
   //  correctLevel : QRCode.CorrectLevel.H
});

   function makeCode () {
      var elText = "haloo";

      qrcode.makeCode(elText);
   }

   makeCode();
</script>