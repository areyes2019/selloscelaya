const dropZone = document.getElementById("dropZone");
    const fileInput = document.getElementById("fileInput");
    const filePreview = document.getElementById("filePreview");
    const dropZoneText = document.getElementById("dropZoneText");

    dropZone.addEventListener("click", () => fileInput.click());

    fileInput.addEventListener("change", (event) => {
        const file = event.target.files[0];
        if (file) {
            dropZoneText.textContent = "Archivo seleccionado: " + file.name;
            filePreview.textContent = "✔ " + file.name;
        }
    });

    dropZone.addEventListener("dragover", (event) => {
        event.preventDefault();
        dropZone.classList.add("dragover");
    });

    dropZone.addEventListener("dragleave", () => {
        dropZone.classList.remove("dragover");
    });

    dropZone.addEventListener("drop", (event) => {
        event.preventDefault();
        dropZone.classList.remove("dragover");

        const file = event.dataTransfer.files[0];
        if (file) {
            fileInput.files = event.dataTransfer.files;
            dropZoneText.textContent = "Archivo seleccionado: " + file.name;
            filePreview.textContent = "✔ " + file.name;
        }
    });