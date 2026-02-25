/**
 * Redimensiona una imagen para mantener la app liviana.
 * Máximo 800px en el lado mayor, JPEG calidad 0.82.
 * @param {File} file
 * @param {number} maxDimension
 * @param {number} quality
 * @returns {Promise<File>}
 */
export function resizeImageFile(file, maxDimension = 800, quality = 0.82) {
    if (!file || !file.type.startsWith('image/')) return Promise.resolve(file);

    return new Promise((resolve, reject) => {
        const img = new Image();
        const url = URL.createObjectURL(file);
        img.onload = () => {
            URL.revokeObjectURL(url);
            const w = img.naturalWidth;
            const h = img.naturalHeight;
            if (w <= maxDimension && h <= maxDimension) {
                resolve(file);
                return;
            }
            const scale = Math.min(maxDimension / w, maxDimension / h);
            const cw = Math.round(w * scale);
            const ch = Math.round(h * scale);
            const canvas = document.createElement('canvas');
            canvas.width = cw;
            canvas.height = ch;
            const ctx = canvas.getContext('2d');
            if (!ctx) {
                resolve(file);
                return;
            }
            ctx.drawImage(img, 0, 0, cw, ch);
            canvas.toBlob(
                (blob) => {
                    if (!blob) {
                        resolve(file);
                        return;
                    }
                    resolve(new File([blob], file.name.replace(/\.[^.]+$/, '.jpg'), { type: 'image/jpeg' }));
                },
                'image/jpeg',
                quality
            );
        };
        img.onerror = () => {
            URL.revokeObjectURL(url);
            resolve(file);
        };
        img.src = url;
    });
}
