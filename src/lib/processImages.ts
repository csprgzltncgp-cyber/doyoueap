import { removeBackground, loadImage } from './removeBackground';
import iphoneImageUrl from '@/assets/iphone_eap_pulse_ok.png';
import riportImageUrl from '@/assets/riport_ok.png';

export async function processIphoneImage(): Promise<string> {
  try {
    const response = await fetch(iphoneImageUrl);
    const blob = await response.blob();
    const img = await loadImage(blob);
    const resultBlob = await removeBackground(img);
    return URL.createObjectURL(resultBlob);
  } catch (error) {
    console.error('Error processing iPhone image:', error);
    return iphoneImageUrl;
  }
}

export async function processRiportImage(): Promise<string> {
  try {
    const response = await fetch(riportImageUrl);
    const blob = await response.blob();
    const img = await loadImage(blob);
    const resultBlob = await removeBackground(img);
    return URL.createObjectURL(resultBlob);
  } catch (error) {
    console.error('Error processing riport image:', error);
    return riportImageUrl;
  }
}
