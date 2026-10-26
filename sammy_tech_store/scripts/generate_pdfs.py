from pathlib import Path
from reportlab.lib import colors
from reportlab.lib.enums import TA_CENTER, TA_LEFT
from reportlab.lib.pagesizes import A4, landscape
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import inch
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, PageBreak, Image, KeepTogether
from reportlab.pdfbase.pdfmetrics import stringWidth
from reportlab.pdfgen import canvas

ROOT = Path(__file__).resolve().parent.parent
OUT = ROOT / "output" / "pdf"
OUT.mkdir(parents=True, exist_ok=True)
LOGO = ROOT / "assets" / "images" / "sammy-tech-logo.jpeg"

NAVY = colors.HexColor("#07182e")
BLUE = colors.HexColor("#057fca")
CYAN = colors.HexColor("#22d3ee")
LIGHT = colors.HexColor("#eef8fc")
INK = colors.HexColor("#162230")
MUTED = colors.HexColor("#52606d")
LINE = colors.HexColor("#d8e3e9")

styles = getSampleStyleSheet()
styles.add(ParagraphStyle(name="GuideTitle", parent=styles["Title"], fontName="Helvetica-Bold", fontSize=25, leading=30, textColor=NAVY, spaceAfter=12))
styles.add(ParagraphStyle(name="GuideSub", parent=styles["Normal"], fontName="Helvetica", fontSize=11, leading=16, textColor=MUTED, spaceAfter=12))
styles.add(ParagraphStyle(name="Section", parent=styles["Heading2"], fontName="Helvetica-Bold", fontSize=16, leading=20, textColor=NAVY, spaceBefore=14, spaceAfter=8))
styles.add(ParagraphStyle(name="Body", parent=styles["BodyText"], fontName="Helvetica", fontSize=9.5, leading=14, textColor=INK, spaceAfter=6))
styles.add(ParagraphStyle(name="Bullet2", parent=styles["BodyText"], fontName="Helvetica", fontSize=9.5, leading=14, leftIndent=16, firstLineIndent=-9, bulletIndent=4, textColor=INK, spaceAfter=4))
styles.add(ParagraphStyle(name="Small", parent=styles["BodyText"], fontName="Helvetica", fontSize=8, leading=11, textColor=MUTED))

def p(text, style="Body"):
    return Paragraph(text, styles[style])

def bullets(items):
    return [Paragraph(item, styles["Bullet2"], bulletText="•") for item in items]

def guide_header_footer(c, doc):
    c.saveState()
    c.setStrokeColor(CYAN); c.setLineWidth(2); c.line(40, A4[1] - 38, A4[0] - 40, A4[1] - 38)
    c.setFont("Helvetica-Bold", 8); c.setFillColor(NAVY); c.drawString(40, A4[1] - 28, "SAMMY TECH - SYSTEM USER GUIDE")
    c.setFont("Helvetica", 8); c.setFillColor(MUTED); c.drawRightString(A4[0] - 40, 24, f"Juba, South Sudan | Page {doc.page}")
    c.restoreState()

def guide_table(rows, widths):
    table = Table(rows, colWidths=widths, repeatRows=1, hAlign="LEFT")
    table.setStyle(TableStyle([
        ("BACKGROUND", (0,0), (-1,0), NAVY), ("TEXTCOLOR", (0,0), (-1,0), colors.white),
        ("FONTNAME", (0,0), (-1,0), "Helvetica-Bold"), ("FONTNAME", (0,1), (-1,-1), "Helvetica"),
        ("FONTSIZE", (0,0), (-1,-1), 8.5), ("LEADING", (0,0), (-1,-1), 11),
        ("GRID", (0,0), (-1,-1), 0.35, LINE), ("VALIGN", (0,0), (-1,-1), "TOP"),
        ("ROWBACKGROUNDS", (0,1), (-1,-1), [colors.white, LIGHT]), ("LEFTPADDING", (0,0), (-1,-1), 7),
        ("RIGHTPADDING", (0,0), (-1,-1), 7), ("TOPPADDING", (0,0), (-1,-1), 7), ("BOTTOMPADDING", (0,0), (-1,-1), 7),
    ]))
    return table

def create_guide():
    target = OUT / "Sammy_Tech_System_User_Guide.pdf"
    doc = SimpleDocTemplate(str(target), pagesize=A4, rightMargin=40, leftMargin=40, topMargin=54, bottomMargin=38)
    story = []
    if LOGO.exists(): story.append(Image(str(LOGO), width=0.86*inch, height=0.86*inch))
    story += [Spacer(1, 8), p("Sammy Tech Online Store", "GuideTitle"), p("System User Guide | Internet and Starlink Accessories Store - Juba, South Sudan", "GuideSub"),
        p("This guide explains how customers and Sammy Tech staff use the completed online store. It covers shopping, payment, order tracking, stock management, installation requests, and reporting."), Spacer(1, 8)]
    story.append(guide_table([[p("Quick access", "Small"), p("Address / purpose", "Small")],
        [p("Customer storefront", "Body"), p("http://localhost/sammy_tech_store/ - Browse products, add to cart, checkout, and track orders.")],
        [p("Staff sign-in", "Body"), p("http://localhost/sammy_tech_store/admin/login.php - Manage orders, stock, payments, installations, and reports.")]], [1.45*inch, 5.55*inch]))
    story += [p("1. Customer Guide", "Section"), p("Customers do not need an account. They can buy as guests using a phone number and an order number."),
        p("Browse and add products", "Section")] + bullets([
        "Open the storefront and use the category buttons to filter Starlink Kits, Routers and Wi-Fi, Accessories, or Installation.",
        "Open the product card, check the USD price and estimated SSP equivalent, then select <b>Add to cart</b>.",
        "The cart keeps multiple products together until the customer is ready to checkout.",
    ]) + [p("Manage the cart", "Section")] + bullets([
        "Use the quantity control to change an item quantity and select <b>Update</b>.",
        "Select <b>Remove</b> to remove an item. The cart total updates automatically.",
        "Select <b>Proceed to checkout</b> when the chosen items are correct.",
    ])
    story += [p("Checkout and payment", "Section"), p("At checkout, enter full name, phone number, optional email, and optional alternative phone. Choose pickup or delivery. Delivery requires an area and landmark/address."),
        guide_table([[p("Payment choice", "Small"), p("Allowed payment method", "Small"), p("What the customer sees", "Small")],
        [p("USD", "Body"), p("Cash or bank transfer", "Body"), p("USD bank account: <b>2010111329047</b>. A reference number and payment proof can be attached.")],
        [p("SSP", "Body"), p("Cash only", "Body"), p("The system calculates the SSP value at <b>1 USD = 7,800 SSP</b>. Bank transfer is automatically disabled.")]], [1.0*inch, 1.65*inch, 4.35*inch]),
        Spacer(1, 8), p("Payment support: 0921628884 | 0917603094 | WhatsApp +211 981 628 884.", "Small"),
        p("Track an order and print a receipt", "Section")] + bullets([
        "After checkout, save the generated order number beginning with ST-.",
        "Open <b>Track order</b>, enter the order number and the same phone number used during checkout.",
        "The tracking result shows order status, payment status, items, and total. Select <b>Print receipt</b> for a printable receipt.",
    ])
    story.append(PageBreak())
    story += [p("2. Staff Guide", "Section"), p("Create the first staff account once from PowerShell before using the dashboard:"),
        p("cd C:\\wamp64\\www\\sammy_tech_store<br/>php scripts/create_admin.php sammy-admin YourSecurePassword123", "Small"),
        p("Dashboard", "Section")] + bullets([
        "Sign in with the staff account. The dashboard shows new orders, pending payments, low-stock products, and completed sales revenue.",
        "Use the order queue to update order status and payment status. Open the order management page for full customer, payment, and proof details.",
        "Use the stock watch list to identify products at or below their reorder level.",
    ]) + [p("Order processing and stock safety", "Section"), p("Orders begin as <b>new</b> and payments begin as <b>pending</b>. Stock is intentionally not deducted at checkout. This protects inventory when a customer abandons or cancels an order."),
        guide_table([[p("Staff action", "Small"), p("System result", "Small")],
        [p("Confirm order", "Body"), p("The system locks product stock, checks availability, deducts quantities, and records an OUT stock movement.")],
        [p("Move to processing / ready / completed", "Body"), p("Updates fulfilment progress while preserving the original stock deduction.")],
        [p("Cancel a confirmed order", "Body"), p("Automatically restores stock and records a RESTORED stock movement.")],
        [p("Update payment", "Body"), p("Set pending, paid, failed, or refunded after checking cash, reference, bank transfer, or uploaded proof.")]], [2.2*inch, 4.8*inch]),
        p("Payment review", "Section")] + bullets([
        "Each order displays the selected payment method, USD or SSP currency, submitted amount, SSP rate used, reference number, and payment proof link if provided.",
        "Payment proofs are stored outside the public website folder and can only be opened by signed-in staff.",
    ])
    story += [p("Products and inventory", "Section")] + bullets([
        "Open <b>Products</b> to add categories, create or edit products, set USD prices, upload a product image, adjust stock, choose a reorder level, and set product status.",
        "Use an adjustment note whenever stock changes so the movement has a reason.",
        "Set a product to draft or out of stock when it should not be sold publicly.",
    ]) + [p("Installation requests and reports", "Section")] + bullets([
        "Open <b>Installations</b> to review requests, contact the customer, and update the request from new to scheduled, in progress, completed, or cancelled.",
        "Open <b>Reports</b> for order totals, daily sales, monthly sales, inventory value, order status counts, and best-selling products.",
        "Use <b>Download CSV</b> in order management to export orders with customer, delivery, USD total, payment currency, payment amount, SSP rate, and status data.",
    ])
    story.append(PageBreak())
    story += [p("3. System Functions and Safety", "Section"),
        guide_table([[p("Function", "Small"), p("How it works", "Small")],
        [p("Guest checkout", "Body"), p("Customers order without creating passwords or accounts."),
         p("Customer tracking", "Body"), p("Requires both order number and matching phone number before a receipt becomes available."),
         p("Currency handling", "Body"), p("Product records remain in USD. SSP is an estimated conversion and the selected payment currency is stored per payment."),
         p("Payment evidence", "Body"), p("JPG, PNG, WEBP, or PDF proof files under 5 MB can be uploaded and viewed only by staff."),
         p("Security", "Body"), p("Password hashing, CSRF form tokens, server validation, prepared SQL statements, HttpOnly sessions, and sign-in attempt throttling are enabled."),
         p("Data checks", "Body"), p("Run <b>php scripts/health_check.php</b> to verify required database tables, payment fields, product count, logo, and exchange-rate setting.")]], [1.75*inch, 5.25*inch]),
        p("Daily operating checklist", "Section")] + bullets([
        "Check new orders and pending payments at the start of the day.",
        "Confirm payment and stock availability before confirming an order.",
        "Update the customer order status as it moves through processing, ready, and completed.",
        "Record stock arrivals and adjustments in Products. Review low-stock alerts before running out.",
        "Update SSP_PER_USD in config/app.php whenever the local exchange rate changes.",
        "Back up the MySQL database before major changes or at the end of each business week.",
    ]) + [p("Contact and support", "Section"), p("Sammy Tech | Juba, South Sudan<br/>Call: 0921628884 or 0917603094<br/>WhatsApp: +211 981 628 884<br/>Email: sammytech1995@gmail.com")]
    doc.build(story, onFirstPage=guide_header_footer, onLaterPages=guide_header_footer)
    return target

class Presentation(canvas.Canvas):
    def __init__(self, filename):
        super().__init__(filename, pagesize=landscape(A4)); self.w, self.h = landscape(A4); self.page = 0
    def slide(self, title, kicker, lines, dark=False, number=True):
        self.page += 1
        self.setFillColor(NAVY if dark else colors.white); self.rect(0,0,self.w,self.h,fill=1,stroke=0)
        self.setFillColor(CYAN); self.rect(0,self.h-18,self.w,18,fill=1,stroke=0)
        if LOGO.exists(): self.drawImage(str(LOGO), 44, self.h-108, 54, 54, mask='auto')
        self.setFillColor(CYAN if dark else BLUE); self.setFont('Helvetica-Bold',10); self.drawString(115,self.h-55,kicker.upper())
        self.setFillColor(colors.white if dark else NAVY); self.setFont('Helvetica-Bold',28); self.drawString(44,self.h-145,title)
        y=self.h-195; self.setFont('Helvetica',15); self.setFillColor(colors.HexColor('#dce9ef') if dark else MUTED)
        for line in lines:
            if line.startswith('• '):
                self.setFillColor(CYAN if dark else BLUE); self.circle(57,y+4,3,fill=1,stroke=0); self.setFillColor(colors.HexColor('#dce9ef') if dark else INK); text=line[2:]; x=72
            else: text=line; x=44
            words=text.split(); current=''; rows=[]
            for word in words:
                trial=(current+' '+word).strip()
                if stringWidth(trial,'Helvetica',15)>690: rows.append(current); current=word
                else: current=trial
            rows.append(current)
            for row in rows: self.drawString(x,y,row); y-=23
            y-=8
        if number:
            self.setFont('Helvetica-Bold',10); self.setFillColor(CYAN if dark else BLUE); self.drawRightString(self.w-42,34,f'SAMMY TECH | {self.page:02d}')
        self.showPage()

def create_presentation():
    target = OUT / "Sammy_Tech_Project_Presentation.pdf"
    deck = Presentation(str(target))
    deck.slide("Sammy Tech Online Store", "Project presentation", ["Design and Implementation of an Online Internet and Starlink Accessories Store", "Case Study: Sammy Tech, Juba, South Sudan", "Smart solutions for a better tomorrow"], dark=True)
    deck.slide("The problem", "Background", ["Customers depend on physical visits, calls, and informal messages to ask about Internet equipment.", "• Product availability and prices are difficult to check.", "• Payment verification and order follow-up are not organized.", "• Stock records and installation work are harder to manage manually."])
    deck.slide("Project goal", "Aim and objectives", ["Build a practical online store that helps Sammy Tech sell Starlink kits, Internet devices, accessories, and installation services in Juba.", "• Show products and real prices online.", "• Support cart, checkout, payment, tracking, receipts, and installations.", "• Give staff control over stock, orders, payments, and reports."], dark=True)
    deck.slide("Technology used", "Implementation", ["PHP for application logic", "MySQL for persistent business data", "HTML, CSS, and JavaScript for the responsive interface", "WAMP / Apache for local deployment", "Session cart, secure staff authentication, and private payment-proof storage"])
    deck.slide("Customer journey", "How it works", ["1. Browse products and filter categories", "2. Add multiple products to a session cart", "3. Enter contact, pickup or delivery, and payment details", "4. Receive an ST order number", "5. Track status with order number plus phone number", "6. Print a verified receipt"], dark=True)
    deck.slide("Payment policy", "USD and SSP", ["USD: cash or bank transfer", "USD bank account: 2010111329047", "SSP: cash only", "Exchange reference: 1 USD = 7,800 SSP", "The system saves the chosen currency, quoted amount, SSP rate, reference, and optional proof."])
    deck.slide("Staff operations", "Administration dashboard", ["• Manage products, images, prices, categories, stock, and reorder levels.", "• Review customer and delivery details before confirming an order.", "• Confirming an order checks stock, deducts inventory, and records a stock movement.", "• Cancelling a confirmed order restores stock automatically.", "• Update payment status, schedule installations, run reports, and export order CSV files."], dark=True)
    deck.slide("Security and reliability", "Quality features", ["Password hashing and staff-only pages", "CSRF protection and server-side validation", "Prepared SQL statements for user data", "HttpOnly and SameSite session cookies", "Login-attempt throttling", "Payment proofs stored outside the public web folder", "Health check for database, products, payment fields, logo, and exchange rate"])
    deck.slide("Result and conclusion", "Project outcome", ["Sammy Tech now has an organized digital workflow for selling Internet and Starlink equipment in Juba.", "The system connects customer ordering with stock control, payment confirmation, installations, tracking, and reporting.", "Future enhancements: payment gateway integration, SMS / WhatsApp notifications, customer accounts, and production HTTPS deployment."], dark=True)
    deck.save()
    return target

if __name__ == '__main__':
    guide = create_guide(); presentation = create_presentation()
    print(guide); print(presentation)
