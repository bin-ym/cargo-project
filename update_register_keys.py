import json
import os

def update_json(file_path, new_keys):
    if not os.path.exists(file_path):
        print(f"File {file_path} not found")
        return
    
    with open(file_path, 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    data.update(new_keys)
    
    with open(file_path, 'w', encoding='utf-8') as f:
        json.dump(data, f, indent=2, ensure_ascii=False)
    print(f"Updated {file_path}")

en_keys = {
    "create_your_account": "Create Your Account",
    "full_name": "Full Name",
    "enter_full_name": "Enter your full name",
    "username": "Username",
    "choose_username": "Choose a username",
    "email": "Email",
    "email_placeholder": "you@example.com",
    "phone_number": "Phone Number",
    "phone_placeholder": "+251 9xx xxx xxx",
    "password": "Password",
    "enter_password": "Enter password",
    "confirm_password": "Confirm Password",
    "confirm_password_placeholder": "Confirm password",
    "role": "Role",
    "select_role": "Select Role",
    "customer": "Customer",
    "transporter": "Transporter",
    "address": "Address",
    "enter_address": "Enter your address",
    "city": "City",
    "enter_city": "Enter your city",
    "license_copy": "License Copy",
    "create_account": "Create Account",
    "already_have_account": "Already have an account?",
    "sign_in": "Sign in",
    "too_short": "Too short",
    "medium_strength": "Medium strength",
    "strong_password": "Strong password",
    "passwords_not_match": "Passwords do not match",
    "creating": "Creating...",
    "server_error_try_again": "Server error, please try again."
}

am_keys = {
    "create_your_account": "አካውንት ይፍጠሩ",
    "full_name": "ሙሉ ስም",
    "enter_full_name": "ሙሉ ስምዎን ያስገቡ",
    "username": "የተጠቃሚ ስም",
    "choose_username": "የተጠቃሚ ስም ይምረጡ",
    "email": "ኢሜል",
    "email_placeholder": "you@example.com",
    "phone_number": "ስልክ ቁጥር",
    "phone_placeholder": "+251 9xx xxx xxx",
    "password": "የይለፍ ቃል",
    "enter_password": "የይለፍ ቃል ያስገቡ",
    "confirm_password": "የይለፍ ቃል ያረጋግጡ",
    "confirm_password_placeholder": "የይለፍ ቃል ያረጋግጡ",
    "role": "ሚና",
    "select_role": "ሚና ይምረጡ",
    "customer": "ደንበኛ",
    "transporter": "አጓጓዥ",
    "address": "አድራሻ",
    "enter_address": "አድራሻዎን ያስገቡ",
    "city": "ከተማ",
    "enter_city": "ከተማዎን ያስገቡ",
    "license_copy": "የመንጃ ፈቃድ ኮፒ",
    "create_account": "አካውንት ፍጠር",
    "already_have_account": "አካውንት አለዎት?",
    "sign_in": "ይግቡ",
    "too_short": "በጣም አጭር",
    "medium_strength": "መካከለኛ ጥንካሬ",
    "strong_password": "ጠንካራ የይለፍ ቃል",
    "passwords_not_match": "የይለፍ ቃላት አይዛመዱም",
    "creating": "በመፍጠር ላይ...",
    "server_error_try_again": "የሰርቨር ስህተት፣ እባክዎ እንደገና ይሞክሩ።"
}

update_json(r'c:\xampp\htdocs\cargo-project\backend\languages\en.json', en_keys)
update_json(r'c:\xampp\htdocs\cargo-project\backend\languages\am.json', am_keys)
