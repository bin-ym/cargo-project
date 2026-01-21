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
    "last_updated": "Last Updated",
    "pp_intro_title": "Introduction",
    "pp_intro_text": "At CargoConnect, we are committed to protecting your privacy. This Privacy Policy explains how we collect, use, and safeguard your information when you use our logistics platform.",
    "pp_info_collect_title": "Information We Collect",
    "pp_info_collect_text": "We collect various types of information to provide and improve our services:",
    "pp_collect_personal": "Personal Information: Name, email address, phone number, and physical address.",
    "pp_collect_shipment": "Shipment Details: Pickup/dropoff locations, cargo descriptions, weight, and dimensions.",
    "pp_collect_location": "Location Data: Real-time GPS coordinates of shipments for tracking purposes.",
    "pp_collect_payment": "Payment Information: Transaction details processed securely via Chapa.",
    "pp_usage_title": "How We Use Your Information",
    "pp_usage_text": "Your information is used for the following purposes:",
    "pp_usage_service": "To facilitate cargo requests and connect customers with transporters.",
    "pp_usage_tracking": "To provide real-time tracking updates to both parties.",
    "pp_usage_comm": "To send notifications regarding shipment status and account updates.",
    "pp_sharing_title": "Information Sharing",
    "pp_sharing_text": "We share relevant shipment details between customers and assigned transporters. We do not sell your personal data to third parties.",
    "pp_security_title": "Data Security",
    "pp_security_text": "We implement industry-standard security measures to protect your data from unauthorized access or disclosure.",
    
    "tos_acceptance_title": "Acceptance of Terms",
    "tos_acceptance_text": "By accessing or using CargoConnect, you agree to be bound by these Terms of Service and all applicable laws in Ethiopia.",
    "tos_service_title": "Description of Service",
    "tos_service_text": "CargoConnect provides a digital platform connecting individuals and businesses with transportation service providers for cargo delivery.",
    "tos_user_resp_title": "User Responsibilities",
    "tos_user_resp_text": "As a user of our platform, you agree to:",
    "tos_resp_accuracy": "Provide accurate and complete information for all requests and profiles.",
    "tos_resp_prohibited": "Not use the service to transport illegal, hazardous, or prohibited items.",
    "tos_resp_security": "Maintain the confidentiality of your account credentials.",
    "tos_payment_title": "Payments and Refunds",
    "tos_payment_text": "Payments are processed through Chapa. All fees are transparently displayed before you confirm a request. Refund policies are subject to specific service agreements.",
    "tos_liability_title": "Limitation of Liability",
    "tos_liability_text": "CargoConnect is a platform and is not liable for direct or indirect damages arising from the transportation services provided by third-party transporters.",
    "tos_governing_law_title": "Governing Law",
    "tos_governing_law_text": "These terms are governed by and construed in accordance with the laws of the Federal Democratic Republic of Ethiopia."
}

am_keys = {
    "last_updated": "ለመጨረሻ ጊዜ የተሻሻለው",
    "pp_intro_title": "መግቢያ",
    "pp_intro_text": "በካርጎ ኮኔክት የእርስዎን ግላዊነት ለመጠበቅ ቆርጠን ተነስተናል። ይህ የግላዊነት ፖሊሲ የእኛን የሎጂስቲክስ መድረክ ሲጠቀሙ የእርስዎን መረጃ እንዴት እንደምንሰበስብ፣ እንደምንጠቀም እና እንደምንጠብቅ ያብራራል።",
    "pp_info_collect_title": "የምንሰበስበው መረጃ",
    "pp_info_collect_text": "አገልግሎቶቻችንን ለማቅረብ እና ለማሻሻል የተለያዩ የመረጃ አይነቶችን እንሰበስባለን፡-",
    "pp_collect_personal": "የግል መረጃ፡ ስም፣ የኢሜል አድራሻ፣ ስልክ ቁጥር እና የመኖሪያ አድራሻ።",
    "pp_collect_shipment": "የጭነት ዝርዝሮች፡ የመጫኛ/የማውረጃ ቦታዎች፣ የጭነት መግለጫዎች፣ ክብደት እና ልኬቶች።",
    "pp_collect_location": "የቦታ መረጃ፡ ለክትትል ዓላማ የጭነቶች የቀጥታ የጂፒኤስ መጋጠሚያዎች።",
    "pp_collect_payment": "የክፍያ መረጃ፡ በቻፓ በኩል ደህንነቱ በተጠበቀ ሁኔታ የተከናወኑ የግብይት ዝርዝሮች።",
    "pp_usage_title": "መረጃዎን እንዴት እንደምንጠቀምበት",
    "pp_usage_text": "የእርስዎ መረጃ ለሚከተሉት ዓላማዎች ይውላል፡-",
    "pp_usage_service": "የጭነት ጥያቄዎችን ለማመቻቸት እና ደንበኞችን ከአጓጓዦች ጋር ለማገናኘት።",
    "pp_usage_tracking": "ለሁለቱም ወገኖች የቀጥታ ክትትል መረጃዎችን ለመስጠት።",
    "pp_usage_comm": "ስለ ጭነት ሁኔታ እና ስለ አካውንት ዝመናዎች ማሳወቂያዎችን ለመላክ።",
    "pp_sharing_title": "መረጃን ማጋራት",
    "pp_sharing_text": "አግባብነት ያላቸውን የጭነት ዝርዝሮች በደንበኞች እና በተመደቡ አጓጓዦች መካከል እናጋራለን። የእርስዎን የግል መረጃ ለሶስተኛ ወገኖች አንሸጥም።",
    "pp_security_title": "የመረጃ ደህንነት",
    "pp_security_text": "የእርስዎን ውሂብ ካልተፈቀደ መዳረሻ ወይም ይፋ ከመሆን ለመጠበቅ የኢንዱስትሪ ደረጃውን የጠበቀ የደህንነት እርምጃዎችን እንተገብራለን።",
    
    "tos_acceptance_title": "ውሎችን መቀበል",
    "tos_acceptance_text": "ካርጎ ኮኔክትን በመጠቀም በእነዚህ የአገልግሎት ውሎች እና በኢትዮጵያ ውስጥ ባሉ ሁሉም ተፈጻሚነት ያላቸው ህጎች ለመገዛት ተስማምተዋል።",
    "tos_service_title": "የአገልግሎት መግለጫ",
    "tos_service_text": "ካርጎ ኮኔክት ግለሰቦችን እና ንግዶችን ለጭነት ማድረስ ከትራንስፖርት አገልግሎት ሰጪዎች ጋር የሚያገናኝ ዲጂታል መድረክ ያቀርባል።",
    "tos_user_resp_title": "የተጠቃሚ ኃላፊነቶች",
    "tos_user_resp_text": "የእኛን መድረክ ተጠቃሚ እንደመሆንዎ መጠን ለሚከተሉት ተስማምተዋል፡-",
    "tos_resp_accuracy": "ለሁሉም ጥያቄዎች እና መገለጫዎች ትክክለኛ እና የተሟላ መረጃ መስጠት።",
    "tos_resp_prohibited": "ህገ-ወጥ፣ አደገኛ ወይም የተከለከሉ እቃዎችን ለማጓጓዝ አገልግሎቱን አለመጠቀም።",
    "tos_resp_security": "የአካውንትዎን ምስጢራዊነት መጠበቅ።",
    "tos_payment_title": "ክፍያዎች እና ተመላሾች",
    "tos_payment_text": "ክፍያዎች በቻፓ በኩል ይከናወናሉ። ጥያቄን ከማረጋገጥዎ በፊት ሁሉም ክፍያዎች በግልጽ ይታያሉ። የተመላሽ ገንዘብ ፖሊሲዎች በተወሰኑ የአገልግሎት ስምምነቶች ላይ የተመሰረቱ ናቸው።",
    "tos_liability_title": "የኃላፊነት ገደብ",
    "tos_liability_text": "ካርጎ ኮኔክት መድረክ ነው እና በሶስተኛ ወገን አጓጓዦች በሚሰጡ የትራንስፖርት አገልግሎቶች ምክንያት ለሚመጡ ቀጥተኛም ሆነ ቀጥተኛ ያልሆኑ ጉዳቶች ተጠያቂ አይደለም።",
    "tos_governing_law_title": "ተፈጻሚነት ያለው ህግ",
    "tos_governing_law_text": "እነዚህ ውሎች በኢትዮጵያ ፌዴራላዊ ዲሞክራሲያዊ ሪፐብሊክ ህጎች መሰረት የሚመሩ እና የሚተረጎሙ ናቸው።"
}

update_json(r'c:\xampp\htdocs\cargo-project\backend\languages\en.json', en_keys)
update_json(r'c:\xampp\htdocs\cargo-project\backend\languages\am.json', am_keys)
