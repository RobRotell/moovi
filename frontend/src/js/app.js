import Alpine from 'alpinejs'
import Moovi from './moovi'


// assigning to window to help browser extensions detect AlpineJS on page
window.Alpine = Alpine


Alpine.data( 'moovi', Moovi )
Alpine.start()
