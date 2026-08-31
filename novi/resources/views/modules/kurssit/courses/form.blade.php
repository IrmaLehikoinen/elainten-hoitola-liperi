<x-app-layout>
    <div class="p-6 max-w-2xl">
        <h1 class="text-xl font-semibold">{{ $course->exists ? 'Muokkaa kurssia' : 'Uusi kurssi' }}</h1>

        @include('kurssit::courses._form', ['course' => $course])
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/heic2any@0.0.4/dist/heic2any.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            async function toJpegIfHeic(file) {
                const name = file.name.toLowerCase();
                const isHeic = file.type === 'image/heic' || file.type === 'image/heif'
                    || name.endsWith('.heic') || name.endsWith('.heif');

                if (!isHeic) {
                    return file;
                }

                try {
                    const converted = await heic2any({ blob: file, toType: 'image/jpeg', quality: 0.9 });
                    const blob = Array.isArray(converted) ? converted[0] : converted;
                    const newName = file.name.replace(/\.(heic|heif)$/i, '.jpg');
                    return new File([blob], newName, { type: 'image/jpeg' });
                } catch (e) {
                    console.error('HEIC-muunnos epäonnistui', e);
                    return file;
                }
            }

            async function compressImageFile(file, maxWidth, quality) {
                try {
                    file = await toJpegIfHeic(file);

                    if (!file.type.startsWith('image/') || file.type === 'image/gif') {
                        return file;
                    }

                    const bitmap = await createImageBitmap(file);
                    const scale = Math.min(1, maxWidth / bitmap.width);
                    const width = Math.round(bitmap.width * scale);
                    const height = Math.round(bitmap.height * scale);

                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(bitmap, 0, 0, width, height);

                    const outputType = file.type === 'image/png' ? 'image/png' : 'image/jpeg';
                    const blob = await new Promise(function (resolve) {
                        canvas.toBlob(resolve, outputType, quality);
                    });

                    if (!blob || blob.size >= file.size) {
                        return file;
                    }

                    const newName = file.name.replace(/\.(png|jpe?g|heic|heif)$/i, outputType === 'image/png' ? '.png' : '.jpg');
                    return new File([blob], newName, { type: outputType });
                } catch (e) {
                    console.error('Kuvan pienennys epäonnistui, käytetään alkuperäistä tiedostoa', e);
                    return file;
                }
            }

            document.querySelectorAll('form').forEach(function (form) {
                let ready = false;

                form.addEventListener('submit', function (event) {
                    if (ready) {
                        return;
                    }

                    const fileInputs = Array.from(form.querySelectorAll('input[type="file"]'))
                        .filter(function (input) { return input.files && input.files[0]; });

                    if (fileInputs.length === 0) {
                        return;
                    }

                    event.preventDefault();

                    const submitButton = event.submitter;
                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.dataset.originalText = submitButton.dataset.originalText || submitButton.textContent;
                        submitButton.textContent = 'Käsitellään kuvaa...';
                    }

                    Promise.all(fileInputs.map(async function (input) {
                        const original = input.files[0];
                        const compressed = await compressImageFile(original, 1600, 0.82);

                        if (compressed !== original) {
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(compressed);
                            input.files = dataTransfer.files;
                        }
                    })).finally(function () {
                        ready = true;
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.textContent = submitButton.dataset.originalText;
                        }
                        if (form.requestSubmit) {
                            form.requestSubmit(submitButton || undefined);
                        } else {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
    @endpush
</x-app-layout>