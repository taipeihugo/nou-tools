import * as leaflet from 'leaflet'
import 'leaflet/dist/leaflet.css'
import markerIcon2xUrl from 'leaflet/dist/images/marker-icon-2x.png'
import markerIconUrl from 'leaflet/dist/images/marker-icon.png'
import markerShadowUrl from 'leaflet/dist/images/marker-shadow.png'

leaflet.Icon.Default.mergeOptions({
  iconRetinaUrl: markerIcon2xUrl,
  iconUrl: markerIconUrl,
  shadowUrl: markerShadowUrl,
})

window.leaflet = leaflet

window.dispatchEvent(new CustomEvent('leafletLoaded'))
