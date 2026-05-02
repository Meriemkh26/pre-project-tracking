/* ========================= */
/* CORE UTILITIES          */
/* ========================= */

let selectedRole = "student";

function setRole(role) {
  selectedRole = role;
  const buttons = document.querySelectorAll(".role");
  buttons.forEach(btn => btn.classList.remove("active"));

  if (role === "student") buttons[0]?.classList.add("active");
  if (role === "teacher") buttons[1]?.classList.add("active");
  if (role === "admin")   buttons[2]?.classList.add("active");

  const roleHint = document.getElementById("roleHint");
  if (roleHint) {
    roleHint.innerText = "Logging in as: " + role.charAt(0).toUpperCase() + role.slice(1);
    roleHint.style.display = "block";
  }
}

/* ========================= */
/* AUTHENTICATION          */
/* ========================= */

function login() {
  const email    = document.getElementById("email")?.value.trim();
  const password = document.getElementById("password")?.value;

  if (!email || !password) {
    showToast("error", "Please enter both email and password");
    shakeElement(document.querySelector(".login-card"));
    return;
  }

  const savedEmail    = localStorage.getItem("userEmail");
  const savedPassword = localStorage.getItem("userPassword");
  const savedRole     = localStorage.getItem("userRole");

  if (email === savedEmail && password === savedPassword) {
    if (selectedRole !== savedRole) {
      showToast("error", "This account is registered as " + savedRole + ". Please select " + savedRole + " role to login.");
      shakeElement(document.querySelector(".login-card"));
      return;
    }

    showToast("success", "Login successful! Redirecting...");
    const redirects = {
      student: "student_dashboard.php",
      teacher: "teacher_dashboard.php",
      admin:   "admin_dashboard.php"
    };
    setTimeout(() => {
      window.location.href = redirects[savedRole] || "login.php";
    }, 1200);
  } else {
    showToast("error", "Invalid email or password");
    shakeElement(document.querySelector(".login-card"));
  }
}

function register() {
  const name     = document.getElementById("fullname")?.value.trim();
  const email    = document.getElementById("email")?.value.trim();
  const password = document.getElementById("password")?.value;
  const confirm  = document.getElementById("confirmPassword")?.value;

  if (!name || !email || !password || !confirm) {
    showToast("error", "Please fill in all fields");
    return;
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    showToast("error", "Please enter a valid email address");
    return;
  }

  if (password !== confirm) {
    showToast("error", "Passwords do not match");
    return;
  }

  if (password.length < 6) {
    showToast("error", "Password must be at least 6 characters");
    return;
  }

  const strength = checkPasswordStrength(password);
  if (strength < 2) {
    showToast("error", "Password is too weak. Include uppercase, numbers, or special characters.");
    return;
  }

  const initials = name.substring(0, 2).toUpperCase();

  localStorage.setItem("userName", name);
  localStorage.setItem("userEmail", email);
  localStorage.setItem("userPassword", password);
  localStorage.setItem("userInitials", initials);
  localStorage.setItem("userRole", selectedRole);

  showToast("success", "Registration successful! Redirecting to login...");
  setTimeout(() => {
    window.location.href = "login.php";
  }, 1500);
}

/* ========================= */
/* PASSWORD STRENGTH       */
/* ========================= */

function checkStrength() {
  const password = document.getElementById("password")?.value;
  const fill = document.getElementById("strength-fill");
  const text = document.getElementById("strength-text");

  if (!password || !fill || !text) return 0;

  let strength = checkPasswordStrength(password);

  const colors = ["#e74c3c", "#e74c3c", "#f39c12", "#3498db", "#27ae60"];
  const labels = ["", "Very Weak", "Weak", "Good", "Strong"];
  const widths = [0, 25, 50, 75, 100];

  fill.style.width = widths[strength] + "%";
  fill.style.background = colors[strength];
  text.innerText = labels[strength];
  text.style.color = colors[strength];

  return strength;
}

function checkPasswordStrength(password) {
  let strength = 0;
  if (password.length > 5) strength++;
  if (/[A-Z]/.test(password)) strength++;
  if (/[0-9]/.test(password)) strength++;
  if (/[^A-Za-z0-9]/.test(password)) strength++;
  return strength;
}

/* ========================= */
/* TOGGLE PASSWORD VIS     */
/* ========================= */

function togglePassword(id, icon) {
  const input = document.getElementById(id);
  if (!input) return;

  if (input.type === "password") {
    input.type = "text";
    icon.classList.replace("fa-eye-slash", "fa-eye");
  } else {
    input.type = "password";
    icon.classList.replace("fa-eye", "fa-eye-slash");
  }
}

function toggleEye(id, icon) {
  const input = document.getElementById(id);
  if (!input) return;

  if (input.type === "password") {
    input.type = "text";
    icon.classList.replace("fa-eye", "fa-eye-slash");
  } else {
    input.type = "password";
    icon.classList.replace("fa-eye-slash", "fa-eye");
  }
}

/* ========================= */
/* TOAST NOTIFICATION      */
/* ========================= */

function showToast(type, message, duration = 3000) {
  const successToast = document.getElementById("successToast");
  const errorToast = document.getElementById("errorToast");

  if (type === "success" && successToast) {
    let messageSpan = successToast.querySelector("span");
    if (!messageSpan) {
      successToast.innerHTML = '<i class="fa-solid fa-circle-check"></i> <span>' + message + '</span>';
    } else {
      messageSpan.innerText = message;
    }
    successToast.classList.add("show");
    successToast.style.animation = "none";
    setTimeout(() => {
      successToast.style.animation = "slideIn 0.4s ease";
    }, 10);
    setTimeout(() => successToast.classList.remove("show"), duration);
  }

  if (type === "error" && errorToast) {
    const errorMsg = document.getElementById("errorMessage");
    if (errorMsg) {
      errorMsg.innerText = message;
    } else {
      errorToast.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> <span>' + message + '</span>';
    }
    errorToast.classList.add("show");
    errorToast.style.animation = "none";
    setTimeout(() => {
      errorToast.style.animation = "slideIn 0.4s ease";
    }, 10);
    setTimeout(() => errorToast.classList.remove("show"), duration + 1000);
  }
}

/* ========================= */
/* MODAL CONFIRMATION      */
/* ========================= */

function showConfirmModal(title, message, iconType, confirmCallback, confirmText = "Delete") {
  let overlay = document.getElementById("confirmModal");

  if (!overlay) {
    overlay = document.createElement("div");
    overlay.id = "confirmModal";
    overlay.className = "modal-overlay";
    overlay.innerHTML = `
      <div class="modal">
        <div class="modal-icon" id="modalIcon"></div>
        <h3 id="modalTitle"></h3>
        <p id="modalMessage"></p>
        <div class="modal-actions">
          <button class="btn" onclick="closeModal()" style="background: #95a5a6;">Cancel</button>
          <button class="btn btn-danger" id="modalConfirmBtn">${confirmText}</button>
        </div>
      </div>
    `;
    document.body.appendChild(overlay);

    overlay.addEventListener("click", function(e) {
      if (e.target === overlay) closeModal();
    });
  }

  document.getElementById("modalTitle").innerText = title;
  document.getElementById("modalMessage").innerText = message;

  const modalIcon = document.getElementById("modalIcon");
  modalIcon.className = "modal-icon " + iconType;
  modalIcon.innerHTML = iconType === "danger"
    ? '<i class="fa-solid fa-triangle-exclamation"></i>'
    : '<i class="fa-solid fa-circle-question"></i>';

  const confirmBtn = document.getElementById("modalConfirmBtn");
  confirmBtn.innerText = confirmText;
  confirmBtn.onclick = function() {
    closeModal();
    if (confirmCallback) confirmCallback();
  };

  overlay.classList.add("show");
}

function closeModal() {
  const overlay = document.getElementById("confirmModal");
  if (overlay) {
    overlay.classList.remove("show");
  }
}

/* ========================= */
/* RENAME MODAL (IMPROVED) */
/* ========================= */

function showRenameModal(fileId) {
  const file = uploadedFiles.find(f => f.id == fileId);
  if (!file) return;

  let overlay = document.getElementById("renameModal");

  if (!overlay) {
    overlay = document.createElement("div");
    overlay.id = "renameModal";
    overlay.className = "modal-overlay";
    overlay.innerHTML = `
      <div class="modal" style="max-width: 480px;">
        <div class="modal-icon" style="background: linear-gradient(135deg, #B488BF, #61a2b1); color: white;">
          <i class="fa-solid fa-pen-to-square"></i>
        </div>
        <h3 style="margin-bottom: 8px;">Rename File</h3>
        <p style="color: #666; font-size: 14px; margin-bottom: 20px;">
          <i class="fa-solid fa-file" style="margin-right: 6px;"></i>
          Current: <strong id="currentFileName" style="color: #333;"></strong>
        </p>
        <div style="margin-bottom: 16px;">
          <label style="font-size: 13px; font-weight: 600; color: #555; margin-bottom: 6px; display: block;">
            New file name
          </label>
          <input type="text" id="newFileName" class="form-input" style="width: 100%;"
                 placeholder="Enter new file name" autocomplete="off">
          <small style="color: #999; font-size: 11px; margin-top: 6px; display: block;">
            <i class="fa-solid fa-circle-info"></i> Include file extension (e.g., .pdf, .docx)
          </small>
        </div>
        <div class="modal-actions">
          <button class="btn" onclick="closeRenameModal()" style="background: #95a5a6; flex: 1;">
            <i class="fa-solid fa-xmark"></i> Cancel
          </button>
          <button class="btn" onclick="confirmRename()" style="background: linear-gradient(135deg, #B488BF, #61a2b1); flex: 1;">
            <i class="fa-solid fa-check"></i> Rename
          </button>
        </div>
      </div>
    `;
    document.body.appendChild(overlay);

    overlay.addEventListener("click", function(e) {
      if (e.target === overlay) closeRenameModal();
    });

    document.addEventListener("keydown", function(e) {
      if (e.key === "Enter" && overlay.classList.contains("show")) {
        confirmRename();
      }
    });
  }

  const fileNameParts = file.name.split('.');
  const extension = fileNameParts.length > 1 ? '.' + fileNameParts.pop() : '';
  const nameWithoutExt = fileNameParts.join('.');

  document.getElementById("currentFileName").innerText = file.name;
  const newFileNameInput = document.getElementById("newFileName");
  newFileNameInput.value = nameWithoutExt;
  overlay.dataset.fileId = fileId;
  overlay.classList.add("show");

  setTimeout(() => {
    newFileNameInput?.focus();
    newFileNameInput?.select();
  }, 100);

  newFileNameInput.dataset.extension = extension;
}

function confirmRename() {
  const overlay = document.getElementById("renameModal");
  const fileId = overlay?.dataset.fileId;
  const newNameInput = document.getElementById("newFileName");
  const newName = newNameInput?.value.trim();

  if (!fileId || !newName) return;

  if (!newName.includes('.')) {
    const extension = newNameInput.dataset.extension || '';
    if (extension) {
      const fullName = newName + extension;
      const file = uploadedFiles.find(f => f.id == fileId);
      if (file && fullName !== file.name) {
        file.name = fullName;
        localStorage.setItem("uploadedFiles", JSON.stringify(uploadedFiles));
        renderFileList();
        showToast("success", "File renamed successfully!");
      }
    } else {
      showToast("error", "Please include a file extension (e.g., .pdf, .docx)");
      return;
    }
  } else {
    const file = uploadedFiles.find(f => f.id == fileId);
    if (file && newName !== file.name) {
      file.name = newName;
      localStorage.setItem("uploadedFiles", JSON.stringify(uploadedFiles));
      renderFileList();
      showToast("success", "File renamed successfully!");
    }
  }

  closeRenameModal();
}

function closeRenameModal() {
  const overlay = document.getElementById("renameModal");
  if (overlay) {
    overlay.classList.remove("show");
  }
}

/* ========================= */
/* LOGOUT                  */
/* ========================= */

function logout() {
  showConfirmModal(
    "Confirm Logout",
    "Are you sure you want to logout? Any unsaved changes will be lost.",
    "warning",
    () => {
      localStorage.clear();
      window.location.href = "/pre-project-tracking/backend/public/index.php?route=logout";
    },
    "Logout"
  );
}

/* ========================= */
/* INITIALIZATION          */
/* ========================= */

document.addEventListener("DOMContentLoaded", function () {

  const name = localStorage.getItem("userName") || "User";
  const initials = name.substring(0, 2).toUpperCase();

  const avatar = document.querySelector(".avatar");
  const nameText = document.querySelector(".user-name-text");

  if (avatar) {
    avatar.innerText = initials;
    avatar.style.animation = "none";
    setTimeout(() => {
      avatar.style.animation = "fadeIn 0.5s ease";
    }, 10);
  }
  if (nameText) nameText.innerText = name;

  const trigger = document.querySelector(".user-trigger");
  const dropdown = document.querySelector(".dropdown");

  if (trigger && dropdown) {
    trigger.addEventListener("click", function (e) {
      e.stopPropagation();
      dropdown.classList.toggle("show");
    });

    document.addEventListener("click", function () {
      dropdown.classList.remove("show");
    });

    dropdown.addEventListener("click", function (e) {
      e.stopPropagation();
    });
  }

  const notifBtn   = document.querySelector(".notif-btn");
  const notifPanel = document.querySelector(".notif-panel");

  if (notifBtn && notifPanel) {
    notifBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      notifPanel.classList.toggle("show");
      const dropdown = document.querySelector(".dropdown");
      if (dropdown) dropdown.classList.remove("show");
    });

    notifPanel.addEventListener("click", function (e) {
      e.stopPropagation();
    });

    document.addEventListener("click", function () {
      notifPanel.classList.remove("show");
    });
  }

  initFileUpload();
  loadProfileData();

  if (document.getElementById("projectsTableBody")) {
    initProjectEvaluation();
  }

  const inputs = document.querySelectorAll(".form-input, .input-box input");
  inputs.forEach(input => {
    input.addEventListener("focus", function() {
      this.parentElement?.classList.add("focused");
    });
    input.addEventListener("blur", function() {
      this.parentElement?.classList.remove("focused");
    });
  });

  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute("href"));
      if (target) {
        target.scrollIntoView({ behavior: "smooth" });
      }
    });
  });

  const passwordInput = document.getElementById("password");
  if (passwordInput) {
    passwordInput.addEventListener("input", checkStrength);
  }

  const roleHint = document.getElementById("roleHint");
  if (roleHint && selectedRole) {
    roleHint.innerText = "Logging in as: " + selectedRole.charAt(0).toUpperCase() + selectedRole.slice(1);
    roleHint.style.display = "block";
  }
});

/* ========================= */
/* SHAKE ANIMATION        */
/* ========================= */

function shakeElement(element) {
  if (!element) return;
  element.style.animation = "none";
  setTimeout(() => {
    element.style.animation = "shake 0.5s ease";
  }, 10);
  setTimeout(() => {
    element.style.animation = "";
  }, 600);
}

/* ========================= */
/* FILE UPLOAD & MANAGEMENT */
/* ========================= */

let uploadedFiles = JSON.parse(localStorage.getItem("uploadedFiles") || "[]");

function initFileUpload() {
  const uploadArea = document.getElementById("uploadArea");
  const fileInput = document.getElementById("fileInput");

  if (!fileInput) return;

  renderFileList();

  if (uploadArea) {
    uploadArea.addEventListener("click", () => fileInput.click());

    uploadArea.addEventListener("dragover", (e) => {
      e.preventDefault();
      uploadArea.style.borderColor = "#B488BF";
      uploadArea.style.background = "#f9f0fb";
      uploadArea.style.transform = "translateY(-3px)";
    });

    uploadArea.addEventListener("dragleave", () => {
      uploadArea.style.borderColor = "#ccc";
      uploadArea.style.background = "#fafafa";
      uploadArea.style.transform = "translateY(0)";
    });

    uploadArea.addEventListener("drop", (e) => {
      e.preventDefault();
      uploadArea.style.borderColor = "#ccc";
      uploadArea.style.background = "#fafafa";
      uploadArea.style.transform = "translateY(0)";

      const files = e.dataTransfer.files;
      if (files.length > 0) {
        handleFiles(files);
      }
    });
  }

  fileInput.addEventListener("change", function() {
    if (this.files.length > 0) {
      handleFiles(this.files);
    }
    this.value = "";
  });
}

function handleFiles(files) {
  const allowedTypes = [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'image/jpeg',
    'image/png',
    'image/gif',
    'text/plain'
  ];

  let addedCount = 0;

  for (let file of files) {
    if (file.size > 10 * 1024 * 1024) {
      showToast("error", 'File "' + file.name + '" is too large. Maximum size is 10MB.');
      continue;
    }

    if (!allowedTypes.includes(file.type) && !file.name.match(/\.(pdf|doc|docx|xls|xlsx|jpg|jpeg|png|gif|txt)$/i)) {
      showToast("error", 'File type not allowed for "' + file.name + '"');
      continue;
    }

    const fileData = {
      id: Date.now() + Math.random(),
      name: file.name,
      size: formatFileSize(file.size),
      sizeBytes: file.size,
      date: new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }),
      type: getFileType(file.name),
      uploadDate: new Date().toISOString()
    };

    uploadedFiles.push(fileData);
    addedCount++;
  }

  if (addedCount > 0) {
    localStorage.setItem("uploadedFiles", JSON.stringify(uploadedFiles));
    renderFileList();
    showToast("success", addedCount + " file(s) uploaded successfully!");
  }
}

function formatFileSize(bytes) {
  if (bytes < 1024) return bytes + " B";
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + " KB";
  return (bytes / (1024 * 1024)).toFixed(1) + " MB";
}

function getFileType(filename) {
  const ext = filename.split('.').pop().toLowerCase();
  if (['pdf'].includes(ext)) return 'pdf';
  if (['doc', 'docx'].includes(ext)) return 'doc';
  if (['xls', 'xlsx'].includes(ext)) return 'xls';
  if (['jpg', 'jpeg', 'png', 'gif', 'bmp'].includes(ext)) return 'img';
  if (['txt', 'md'].includes(ext)) return 'text';
  return 'file';
}

function getFileIconClass(type) {
  const iconMap = {
    'pdf': 'fa-file-pdf',
    'doc': 'fa-file-word',
    'xls': 'fa-file-excel',
    'img': 'fa-file-image',
    'text': 'fa-file-lines',
    'file': 'fa-file'
  };
  return iconMap[type] || 'fa-file';
}

function renderFileList() {
  const fileList = document.getElementById("fileList");
  if (!fileList) return;

  if (uploadedFiles.length === 0) {
    fileList.innerHTML = `
      <div style="text-align:center; padding:40px 20px; color:#999;">
        <i class="fa-solid fa-cloud-arrow-up" style="font-size:48px; margin-bottom:12px; display:block; opacity:0.5;"></i>
        <p style="font-size:15px; font-weight:500;">No files uploaded yet</p>
        <p style="font-size:13px; margin-top:6px;">Drag & drop files here or click to browse</p>
      </div>
    `;
    return;
  }

  fileList.innerHTML = uploadedFiles.map(file => `
    <div class="file-item" data-id="${file.id}" style="animation: fadeIn 0.3s ease;">
      <div class="file-info">
        <div class="file-icon">
          <i class="fa-solid ${getFileIconClass(file.type)}"></i>
        </div>
        <div class="file-details">
          <p style="font-weight:500;">${file.name}</p>
          <small>${file.date} • ${file.size}</small>
        </div>
      </div>
      <div class="table-actions">
        <button class="btn btn-sm" onclick="downloadFile('${file.id}')" data-tooltip="Download">
          <i class="fa-solid fa-file-arrow-down"></i>
        </button>
        <button class="btn btn-sm" onclick="showRenameModal('${file.id}')" data-tooltip="Rename">
          <i class="fa-solid fa-pen-to-square"></i>
        </button>
        <button class="btn btn-sm btn-danger" onclick="confirmDeleteFile('${file.id}')" data-tooltip="Delete">
          <i class="fa-solid fa-trash"></i>
        </button>
      </div>
    </div>
  `).join('');
}

function downloadFile(id) {
  const file = uploadedFiles.find(f => f.id == id);
  if (file) {
    showToast("success", 'Downloading "' + file.name + '"...');
  }
}

function confirmDeleteFile(id) {
  const file = uploadedFiles.find(f => f.id == id);
  if (!file) return;

  showConfirmModal(
    "Delete File",
    'Are you sure you want to delete "' + file.name + '"? This action cannot be undone.',
    "danger",
    () => deleteFile(id),
    "Delete"
  );
}

function deleteFile(id) {
  const file = uploadedFiles.find(f => f.id == id);
  uploadedFiles = uploadedFiles.filter(f => f.id != id);
  localStorage.setItem("uploadedFiles", JSON.stringify(uploadedFiles));
  renderFileList();
  showToast("success", 'File "' + (file?.name || 'Unknown') + '" deleted successfully!');
}

/* ========================= */
/* PROFILE DATA & SAVE      */
/* ========================= */

function loadProfileData() {
  const name     = localStorage.getItem("userName")     || "";
  const email    = localStorage.getItem("userEmail")    || "";
  const initials = localStorage.getItem("userInitials") || "??";
  const role     = localStorage.getItem("userRole")     || "student";

  const profileAvatar = document.getElementById("profileAvatar");
  const profileName   = document.getElementById("profileName");
  const profileEmail  = document.getElementById("profileEmail");
  const profileRole   = document.getElementById("profileRole");

  if (profileAvatar) {
    profileAvatar.innerText = initials;
    profileAvatar.style.animation = "none";
    setTimeout(() => {
      profileAvatar.style.animation = "fadeIn 0.5s ease";
    }, 10);
  }
  if (profileName)   profileName.innerText   = name  || "No name set";
  if (profileEmail)  profileEmail.innerText  = email || "";
  if (profileRole)   profileRole.innerText   = role.charAt(0).toUpperCase() + role.slice(1);

  const editName  = document.getElementById("editName");
  const editEmail = document.getElementById("editEmail");

  if (editName)  editName.value  = name;
  if (editEmail) editEmail.value = email;

  const editStudentId = document.getElementById("editStudentId");
  if (editStudentId) editStudentId.value = localStorage.getItem("userStudentId") || "";

  const editDept = document.getElementById("editDept");
  const editSpecialization = document.getElementById("editSpecialization");
  if (editDept) editDept.value = localStorage.getItem("userDept") || "";
  if (editSpecialization) editSpecialization.value = localStorage.getItem("userSpecialization") || "";

  const editAdminLevel = document.getElementById("editAdminLevel");
  const editPermissions = document.getElementById("editPermissions");
  if (editAdminLevel) editAdminLevel.value = localStorage.getItem("adminLevel") || "";
  if (editPermissions) editPermissions.value = localStorage.getItem("adminPermissions") || "";
}

function saveProfile() {
  const name      = document.getElementById("editName")?.value.trim();
  const email     = document.getElementById("editEmail")?.value.trim();
  const studentId = document.getElementById("editStudentId")?.value.trim() || "";
  const current   = document.getElementById("currentPassword")?.value || "";
  const newPass   = document.getElementById("newPassword")?.value || "";
  const confirm   = document.getElementById("confirmNewPassword")?.value || "";

  if (!name || !email) {
    showToast("error", "Name and email are required.");
    return;
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    showToast("error", "Please enter a valid email address.");
    return;
  }

  if (newPass || confirm || current) {
    const savedPass = localStorage.getItem("userPassword");
    if (current !== savedPass) {
      showToast("error", "Current password is incorrect.");
      return;
    }
    if (newPass !== confirm) {
      showToast("error", "New passwords do not match.");
      return;
    }
    if (newPass.length < 6) {
      showToast("error", "New password must be at least 6 characters.");
      return;
    }
    localStorage.setItem("userPassword", newPass);
  }

  const initials = name.substring(0, 2).toUpperCase();
  localStorage.setItem("userName",     name);
  localStorage.setItem("userEmail",    email);
  localStorage.setItem("userInitials", initials);
  localStorage.setItem("userStudentId", studentId);

  updateUIAfterProfileSave(initials, name, email);
}

function saveTeacherProfile() {
  const name           = document.getElementById("editName")?.value.trim();
  const email          = document.getElementById("editEmail")?.value.trim();
  const dept           = document.getElementById("editDept")?.value.trim() || "";
  const specialization = document.getElementById("editSpecialization")?.value.trim() || "";
  const current        = document.getElementById("currentPassword")?.value || "";
  const newPass        = document.getElementById("newPassword")?.value || "";
  const confirm        = document.getElementById("confirmNewPassword")?.value || "";

  if (!name || !email) {
    showToast("error", "Name and email are required.");
    return;
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    showToast("error", "Please enter a valid email address.");
    return;
  }

  if (newPass || confirm || current) {
    const savedPass = localStorage.getItem("userPassword");
    if (current !== savedPass) {
      showToast("error", "Current password is incorrect.");
      return;
    }
    if (newPass !== confirm) {
      showToast("error", "New passwords do not match.");
      return;
    }
    if (newPass.length < 6) {
      showToast("error", "New password must be at least 6 characters.");
      return;
    }
    localStorage.setItem("userPassword", newPass);
  }

  const initials = name.substring(0, 2).toUpperCase();
  localStorage.setItem("userName",     name);
  localStorage.setItem("userEmail",    email);
  localStorage.setItem("userInitials", initials);
  localStorage.setItem("userDept", dept);
  localStorage.setItem("userSpecialization", specialization);

  updateUIAfterProfileSave(initials, name, email);
}

function saveAdminProfile() {
  const name        = document.getElementById("editName")?.value.trim();
  const email       = document.getElementById("editEmail")?.value.trim();
  const adminLevel  = document.getElementById("editAdminLevel")?.value.trim() || "";
  const permissions = document.getElementById("editPermissions")?.value.trim() || "";
  const current     = document.getElementById("currentPassword")?.value || "";
  const newPass     = document.getElementById("newPassword")?.value || "";
  const confirm     = document.getElementById("confirmNewPassword")?.value || "";

  if (!name || !email) {
    showToast("error", "Name and email are required.");
    return;
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    showToast("error", "Please enter a valid email address.");
    return;
  }

  if (newPass || confirm || current) {
    const savedPass = localStorage.getItem("userPassword");
    if (current !== savedPass) {
      showToast("error", "Current password is incorrect.");
      return;
    }
    if (newPass !== confirm) {
      showToast("error", "New passwords do not match.");
      return;
    }
    if (newPass.length < 6) {
      showToast("error", "New password must be at least 6 characters.");
      return;
    }
    localStorage.setItem("userPassword", newPass);
  }

  const initials = name.substring(0, 2).toUpperCase();
  localStorage.setItem("userName",     name);
  localStorage.setItem("userEmail",    email);
  localStorage.setItem("userInitials", initials);
  localStorage.setItem("adminLevel", adminLevel);
  localStorage.setItem("adminPermissions", permissions);

  updateUIAfterProfileSave(initials, name, email);
}

function updateUIAfterProfileSave(initials, name, email) {
  const profileAvatar = document.getElementById("profileAvatar");
  const profileName   = document.getElementById("profileName");
  const profileEmail  = document.getElementById("profileEmail");

  if (profileAvatar) {
    profileAvatar.innerText = initials;
    profileAvatar.style.animation = "none";
    setTimeout(() => {
      profileAvatar.style.animation = "scaleIn 0.4s ease";
    }, 10);
  }
  if (profileName)  profileName.innerText  = name;
  if (profileEmail) profileEmail.innerText = email;

  document.querySelectorAll(".avatar").forEach(avatar => {
    avatar.innerText = initials;
  });
  document.querySelectorAll(".user-name-text").forEach(nameText => {
    nameText.innerText = name;
  });

  ["currentPassword", "newPassword", "confirmNewPassword"].forEach(id => {
    const input = document.getElementById(id);
    if (input) input.value = "";
  });

  showToast("success", "Profile updated successfully!");
}

/* ========================= */
/* PROJECT EVALUATION       */
/* ========================= */

let projectsData = JSON.parse(localStorage.getItem("projectsData") || "[]");

function initProjectEvaluation() {
  if (projectsData.length === 0) {
    projectsData = [
      {
        id: 1,
        title: "Medical Clinic Management System",
        student: "Sara B.",
        status: "pending",
        grade: "",
        feedback: "",
        submittedDate: "2026-05-10"
      },
      {
        id: 2,
        title: "E-Learning Platform",
        student: "Amine K.",
        status: "accepted",
        grade: "85",
        feedback: "Good work on the UI design.",
        submittedDate: "2026-05-08"
      },
      {
        id: 3,
        title: "Smart Library System",
        student: "Lina M.",
        status: "in_progress",
        grade: "",
        feedback: "",
        submittedDate: "2026-05-05"
      }
    ];
    localStorage.setItem("projectsData", JSON.stringify(projectsData));
  }
  renderProjectsTable();
}

function renderProjectsTable() {
  const tableBody = document.getElementById("projectsTableBody");
  if (!tableBody) return;

  tableBody.innerHTML = projectsData.map(project => `
    <tr style="animation: fadeIn 0.3s ease;">
      <td>
        <strong style="font-size:15px;">${project.title}</strong><br>
        <small class="light-text">Student: ${project.student}</small>
      </td>
      <td>
        <span class="badge ${project.status}">${project.status.charAt(0).toUpperCase() + project.status.slice(1).replace('_', ' ')}</span>
      </td>
      <td>
        <input type="number" class="grade-input" value="${project.grade}" min="0" max="100"
               placeholder="0-100" onchange="updateGrade(${project.id}, this.value)">
      </td>
      <td>
        <textarea class="evaluate-textarea" rows="3" placeholder="Add feedback..."
                  onchange="updateFeedback(${project.id}, this.value)">${project.feedback || ''}</textarea>
      </td>
      <td>
        <button class="btn btn-sm btn-success" onclick="saveEvaluation(${project.id})">
          <i class="fa-solid fa-check"></i> Save
        </button>
      </td>
    </tr>
  `).join('');
}

function updateGrade(id, value) {
  const project = projectsData.find(p => p.id === id);
  if (project) {
    project.grade = value;
    localStorage.setItem("projectsData", JSON.stringify(projectsData));
  }
}

function updateFeedback(id, value) {
  const project = projectsData.find(p => p.id === id);
  if (project) {
    project.feedback = value;
    localStorage.setItem("projectsData", JSON.stringify(projectsData));
  }
}

function saveEvaluation(id) {
  const project = projectsData.find(p => p.id === id);
  if (!project) return;

  if (!project.grade || parseInt(project.grade) < 0 || parseInt(project.grade) > 100) {
    showToast("error", "Please enter a valid grade between 0 and 100");
    return;
  }

  project.status = "accepted";
  localStorage.setItem("projectsData", JSON.stringify(projectsData));
  renderProjectsTable();
  showToast("success", 'Evaluation saved for "' + project.title + '"');
}

/* ========================= */
/* USER/PROJECT MANAGEMENT  */
/* ========================= */

function editUser(id, type) {
  const name = prompt("Enter new name:");
  const email = prompt("Enter new email:");
  if (name && email) {
    showToast("success", "User updated successfully!");
  }
}

function deleteUser(id, name) {
  showConfirmModal(
    "Delete User",
    'Are you sure you want to delete "' + name + '"?',
    "danger",
    () => { showToast("success", 'User "' + name + '" deleted!'); },
    "Delete"
  );
}

function editProject(id) {
  const newTitle = prompt("Enter new project title:");
  if (newTitle && newTitle.trim()) {
    showToast("success", "Project updated successfully!");
  }
}

function deleteProject(id, title) {
  showConfirmModal(
    "Delete Project",
    'Are you sure you want to delete "' + title + '"?',
    "danger",
    () => { showToast("success", 'Project "' + title + '" deleted!'); },
    "Delete"
  );
}

/* ========================= */
/* KEYBOARD SHORTCUTS      */
/* ========================= */

document.addEventListener("keydown", function(e) {
  if (e.key === "Escape") {
    closeModal();
    closeRenameModal();
    const notifPanel = document.querySelector(".notif-panel");
    const dropdown = document.querySelector(".dropdown");
    if (notifPanel) notifPanel.classList.remove("show");
    if (dropdown) dropdown.classList.remove("show");
  }
});

/* ========================= */
/* SMOOTH PAGE TRANSITION   */
/* ========================= */

window.addEventListener("beforeunload", function() {
  document.body.style.opacity = "0.8";
  document.body.style.transition = "opacity 0.2s ease";
});