# -*- coding: utf-8 -*-
"""SOULSILVER árlista PDF.

A beépített Helvetica NEM tartalmazza az 'ő' és 'ű' karaktereket (U+0151,
U+0171), ezért Arial TrueType fontot regisztrálunk.

Az adatok itt közvetlenül, ékezetesen vannak leírva — korábbi verzióban
ASCII + csere-tábla volt, és két tétel kimaradt belőle.
"""
import os
from reportlab.lib import colors
from reportlab.lib.enums import TA_RIGHT
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle
from reportlab.lib.units import mm
from reportlab.pdfbase import pdfmetrics
from reportlab.pdfbase.ttfonts import TTFont
from reportlab.platypus import (BaseDocTemplate, Frame, PageTemplate, Paragraph,
                                Spacer, Table, TableStyle, KeepTogether)

OUT = r'C:\Users\SOULSILVER\Downloads\SOULSILVER-arlista.pdf'
FONTDIR = r'C:\Windows\Fonts'
pdfmetrics.registerFont(TTFont('Ari', os.path.join(FONTDIR, 'arial.ttf')))
pdfmetrics.registerFont(TTFont('Ari-B', os.path.join(FONTDIR, 'arialbd.ttf')))
pdfmetrics.registerFontFamily('Ari', normal='Ari', bold='Ari-B')

INK = colors.HexColor('#0D1013')
SOFT = colors.HexColor('#565F6B')
FAINT = colors.HexColor('#87909C')
MINT = colors.HexColor('#0A9E77')
MINT_BG = colors.HexColor('#E8FBF4')
EDGE = colors.HexColor('#DCE0E5')
GROUND = colors.HexColor('#F4F6F7')

S = {
    'h1': ParagraphStyle('h1', fontName='Ari-B', fontSize=26, leading=29, textColor=INK, spaceAfter=2),
    'sub': ParagraphStyle('sub', fontName='Ari', fontSize=10.5, leading=15, textColor=SOFT),
    'h2': ParagraphStyle('h2', fontName='Ari-B', fontSize=14.5, leading=18, textColor=INK,
                         spaceBefore=2, spaceAfter=1, keepWithNext=True),
    'h2n': ParagraphStyle('h2n', fontName='Ari', fontSize=8.5, leading=11, textColor=MINT,
                          keepWithNext=True),
    'pkg': ParagraphStyle('pkg', fontName='Ari-B', fontSize=11.5, leading=14, textColor=INK),
    'pkgsub': ParagraphStyle('pkgsub', fontName='Ari', fontSize=8.5, leading=11, textColor=FAINT),
    'price': ParagraphStyle('price', fontName='Ari-B', fontSize=13, leading=15, textColor=MINT,
                            alignment=TA_RIGHT),
    'feat': ParagraphStyle('feat', fontName='Ari', fontSize=9, leading=13.5, textColor=SOFT,
                           leftIndent=8, bulletIndent=0, spaceBefore=1),
    'note': ParagraphStyle('note', fontName='Ari', fontSize=8.5, leading=12, textColor=FAINT),
    'body': ParagraphStyle('body', fontName='Ari', fontSize=9.5, leading=14, textColor=SOFT),
    'th': ParagraphStyle('th', fontName='Ari-B', fontSize=8.5, leading=11, textColor=FAINT),
    'td': ParagraphStyle('td', fontName='Ari', fontSize=9.5, leading=13, textColor=INK),
    'tdr': ParagraphStyle('tdr', fontName='Ari-B', fontSize=9.5, leading=13, textColor=INK,
                          alignment=TA_RIGHT),
}

# ---------------------------------------------------------------- adatok
# (név, elszámolás, [(csomag, ár, alcím, kiemelt?, [tartalom...])], megjegyzés)
SERVICES = [
    ('PPC hirdetéskezelés', 'Havi kezelési díj', [
        ('Starter', '90 000 Ft/hó-tól', '1 platform, induló keret', False, [
            '1 platform kezelése (Meta vagy Google)',
            'Kampány beállítás és tracking',
            'Heti optimalizálás',
            'Havi riport']),
        ('Growth', '180 000 Ft/hó-tól', 'Több platform, aktív skálázás', True, [
            '2–3 platform (Meta, Google, TikTok)',
            'Kreatívgyártás és A/B tesztelés',
            'Napi optimalizálás és bidkezelés',
            'Konverziómérés (pixel + API)',
            'Heti riport és konzultáció']),
        ('Scale', 'Egyedi ár', 'Nagy keret / %-alapú', False, [
            'Korlátlan platform és kampány',
            'Dedikált kampánymenedzser',
            'Fejlett attribúció és feed-kezelés',
            'Ügyfél-hozzáférés a SOULSILVER CRM-hez']),
    ], 'A kezelési díj a hirdetési kereten FELÜL értendő — a hirdetési költést '
       'közvetlenül a platformnak (Meta, Google, TikTok) fizeted, a saját fiókodból.'),

    ('Workflow automatizálás', 'Projektalapú, opcionális üzemeltetéssel', [
        ('Feltárás', '90 000 Ft-tól', 'Felmérés és workflow-térkép', False, [
            'Folyamatfelmérés interjúkkal',
            'Workflow-térkép prioritásokkal',
            'Becsült megtakarítás folyamatonként',
            'Beszámítjuk, ha továbbmegyünk']),
        ('Egy workflow', '180 000 Ft-tól', 'Egy folyamat, éles bekötéssel', True, [
            'Egy folyamat teljes automatizálása',
            'Rendszerek bekötése, tesztelés',
            'Hibakezelés és értesítések',
            'Betanítás a csapatnak',
            '30 nap utánkövetés']),
        ('Teljes csomag', '490 000 Ft-tól', '4–6 workflow, üzemeltetéssel', False, [
            '4–6 folyamat automatizálása',
            'Rendszerek közti adatszinkron',
            'Dokumentált folyamatleírások',
            'Havi üzemeltetés 39 000 Ft/hó-tól',
            'Negyedéves felülvizsgálat']),
    ], 'A havi üzemeltetés (39 000 Ft/hó-tól) opcionális: a már élő workflow-k '
       'figyelése, hibajavítás és ápolás tartozik bele.'),

    ('Márkaidentitás', 'Projektalapú', [
        ('Logó csomag', '120 000 Ft-tól', 'Alap identitás', False, [
            'Logó (2 koncepció)',
            'Színpaletta',
            'Betűtípus ajánlás',
            'Fájlok minden formátumban']),
        ('Teljes arculat', '350 000 Ft-tól', 'Komplett vizuális rendszer', True, [
            'Logó és jelrendszer',
            'Teljes színpaletta és tipográfia',
            'Arculati kézikönyv',
            'Social + prezentáció sablonok',
            'Névjegy és fejléc']),
        ('Rebrand', 'Egyedi ár', 'Meglévő márka megújítása', False, [
            'Márkaaudit',
            'Stratégia',
            'Teljes arculat',
            'Bevezetési terv']),
    ], None),

    ('Közösségi média', 'Havi díj', [
        ('Starter', '120 000 Ft/hó-tól', 'Alap jelenlét', False, [
            '2 platform kezelése',
            'Heti tartalom',
            'Közösségkezelés',
            'Havi riport']),
        ('Growth', '250 000 Ft/hó-tól', 'Növekedés + hirdetés', True, [
            '3–4 platform',
            'Napi tartalom',
            'Fizetett hirdetések kezelése',
            'Kreatívgyártás',
            'Részletes riport']),
        ('Scale', 'Egyedi ár', 'Full-service', False, [
            'Teljes körű menedzsment',
            'Dedikált csapat',
            'Influencer kampányok',
            'A/B tesztelés']),
    ], None),

    ('Weboldalkészítés', 'Projektalapú', [
        ('Landing', '150 000 Ft-tól', '1 oldalas landing', False, [
            'Egyedi 1 oldal',
            'Reszponzív',
            'Kontakt űrlap',
            'Alap SEO',
            'Analitika']),
        ('Üzleti weboldal', '350 000 Ft-tól', '5–8 aloldal', True, [
            'Egyedi design',
            '5–8 aloldal',
            'CMS (szerkeszthető)',
            'SEO-alapok',
            'Analitika és mérés',
            '1 hó support']),
        ('Webshop / Egyedi', 'Egyedi ár', 'Webáruház vagy egyedi funkció', False, [
            'Webshop',
            'Fizetés és szállítás',
            'Egyedi funkciók',
            'Integrációk',
            'Dedikált támogatás']),
    ], None),

    ('Drónfelvétel', 'Projektalapú', [
        ('Alap', '80 000 Ft-tól', '1 helyszín, rövid anyag', False, [
            '1 helyszín, kb. 1 óra forgatás',
            'Nyers + alapvágás (30–60 mp)',
            '1 export formátum',
            'Alap színkorrekció']),
        ('Pro', '180 000 Ft-tól', 'Több helyszín, cinematic vágás', True, [
            'Akár 2–3 helyszín',
            'Cinematic vágás + color grading',
            'Licenszelt zene',
            'Több platform-export (Reels/Shorts/web)',
            'Engedélyeztetés intézése']),
        ('Prémium', 'Egyedi ár', 'Kampányra hangolt csomag', False, [
            'Több napos / több helyszínes forgatás',
            'Teljes kampánycsomag (teaser + full)',
            'Storyboard és kreatív koncepció',
            'Dedikált kapcsolattartó']),
    ], None),

    ('Aftermovie', 'Projektalapú', [
        ('Esemény', '150 000 Ft-tól', 'Alap aftermovie', False, [
            '1 kamera + alap drón',
            '1–2 perces film',
            'Licenszelt zene',
            '1 export']),
        ('Prémium', '300 000 Ft-tól', 'Több kamera + drón', True, [
            '2–3 kameraállás',
            'Drónfelvételek',
            'Teaser + full változat',
            'Color grading',
            'Prioritásos leadás']),
        ('Sorozat', 'Egyedi ár', 'Keretszerződés', False, [
            'Több esemény',
            'Egységes arculat',
            'Dedikált stáb',
            'Gyorsított átfutás']),
    ], None),

    ('Termékfotózás', 'Projektalapú', [
        ('Alap', '50 000 Ft-tól', 'Packshot', False, [
            'Akár 10 termék',
            'Fehér háttér',
            'Alap retus',
            'Webshop-kész export']),
        ('Pro', '120 000 Ft-tól', 'Packshot + lifestyle', True, [
            'Akár 25 termék',
            'Packshot + lifestyle',
            'Profi retus',
            'Több platform export',
            'Több beállítás']),
        ('Kampány', 'Egyedi ár', 'Kampány csomag', False, [
            'Koncepció és moodboard',
            'Modell',
            'Helyszín',
            'Videó opció',
            'Dedikált kapcsolattartó']),
    ], None),

    ('SOULSILVER CRM', 'Havi előfizetés', [
        ('Starter', '9 900 Ft/hó', 'Egyéni / induló', False, [
            '2 platform összekötése',
            '1 dashboard',
            'Heti riport',
            'E-mail támogatás']),
        ('Pro', '24 900 Ft/hó', 'Növekvő csapatok', True, [
            '5+ platform',
            'Korlátlan dashboard',
            'Automatikus riasztások',
            'Ügyfél-hozzáférés',
            'Prioritásos támogatás']),
        ('Agency', 'Egyedi ár', 'Ügynökségeknek', False, [
            'Korlátlan fiók',
            'White-label riportok',
            'API hozzáférés',
            'Dedikált kapcsolattartó']),
    ], 'Korai hozzáférés — az árak bevezető jellegűek, a szolgáltatás '
       'folyamatosan bővül.'),
]

# ---------------------------------------------------------------- rajzolás
PW, PH = A4
MARGIN = 17 * mm
CW = PW - 2 * MARGIN


def header_footer(canv, doc):
    canv.saveState()
    canv.setFont('Ari-B', 9)
    canv.setFillColor(INK)
    canv.drawString(MARGIN, PH - 12 * mm, 'SOULSILVER.')
    canv.setFont('Ari', 8)
    canv.setFillColor(FAINT)
    canv.drawRightString(PW - MARGIN, PH - 12 * mm, 'Árlista · 2026')
    canv.setStrokeColor(EDGE)
    canv.setLineWidth(0.6)
    canv.line(MARGIN, PH - 14.5 * mm, PW - MARGIN, PH - 14.5 * mm)

    canv.line(MARGIN, 14 * mm, PW - MARGIN, 14 * mm)
    canv.setFont('Ari', 7.5)
    canv.setFillColor(FAINT)
    canv.drawString(MARGIN, 10 * mm, 'info@soulsilvermarketing.com · soulsilver.hu')
    canv.drawRightString(PW - MARGIN, 10 * mm, '%d. oldal' % doc.page)
    canv.restoreState()


def pkg_block(name, price, subtitle, featured, feats):
    left = [Paragraph(name, S['pkg'])]
    if subtitle:
        left.append(Paragraph(subtitle, S['pkgsub']))
    head = Table(
        [[left, Paragraph(price, S['price'])]],
        colWidths=[CW * 0.62, CW * 0.38 - 12])
    head.setStyle(TableStyle([
        ('VALIGN', (0, 0), (-1, -1), 'MIDDLE'),
        ('BACKGROUND', (0, 0), (-1, -1), MINT_BG if featured else GROUND),
        ('LEFTPADDING', (0, 0), (-1, -1), 9),
        ('RIGHTPADDING', (0, 0), (-1, -1), 9),
        ('TOPPADDING', (0, 0), (-1, -1), 6),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 6),
        ('LINEBELOW', (0, 0), (-1, -1), 1.4 if featured else 0.6, MINT if featured else EDGE),
    ]))
    items = [Paragraph(f, S['feat'], bulletText='·') for f in feats]
    return KeepTogether([head, Spacer(1, 4)] + items + [Spacer(1, 9)])


def build():
    doc = BaseDocTemplate(OUT, pagesize=A4,
                          leftMargin=MARGIN, rightMargin=MARGIN,
                          topMargin=20 * mm, bottomMargin=18 * mm,
                          title='SOULSILVER árlista', author='SOULSILVER Marketing Agency')
    frame = Frame(MARGIN, 18 * mm, CW, PH - 38 * mm, id='f')
    doc.addPageTemplates([PageTemplate(id='p', frames=[frame], onPage=header_footer)])

    st = []
    st.append(Paragraph('Árlista', S['h1']))
    st.append(Spacer(1, 3))
    st.append(Paragraph(
        'SOULSILVER Marketing Agency · minden szolgáltatás és csomag, '
        'a hozzá tartozó tartalommal.', S['sub']))
    st.append(Spacer(1, 10))

    rows = [[Paragraph('Szolgáltatás', S['th']),
             Paragraph('Elszámolás', S['th']),
             Paragraph('Belépő ár', S['th'])]]
    for name, billing, pkgs, _note in SERVICES:
        rows.append([Paragraph(name, S['td']),
                     Paragraph(billing, S['td']),
                     Paragraph(pkgs[0][1], S['tdr'])])
    t = Table(rows, colWidths=[CW * 0.42, CW * 0.35, CW * 0.23], repeatRows=1)
    t.setStyle(TableStyle([
        ('VALIGN', (0, 0), (-1, -1), 'MIDDLE'),
        ('LINEBELOW', (0, 0), (-1, 0), 0.8, INK),
        ('LINEBELOW', (0, 1), (-1, -2), 0.4, EDGE),
        ('TOPPADDING', (0, 0), (-1, -1), 5),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 5),
        ('LEFTPADDING', (0, 0), (-1, -1), 0),
        ('RIGHTPADDING', (0, 0), (-1, -1), 0),
    ]))
    st.append(t)
    st.append(Spacer(1, 8))
    st.append(Paragraph(
        'Az árak <b>nettó, tájékoztató kiindulási árak</b>. A végleges ajánlat '
        'a terjedelem és a határidő alapján, egyeztetés után készül. '
        'Az „Egyedi ár" azt jelenti, hogy a csomag tartalma projektre szabott.',
        S['note']))
    st.append(Spacer(1, 14))

    for name, billing, pkgs, note in SERVICES:
        st.append(Paragraph(name, S['h2']))
        st.append(Paragraph(billing.upper(), S['h2n']))
        st.append(Spacer(1, 6))
        for p in pkgs:
            st.append(pkg_block(*p))
        if note:
            st.append(Paragraph(note, S['note']))
        st.append(Spacer(1, 14))

    st.append(Paragraph('Kapcsolat', S['h2']))
    st.append(Spacer(1, 4))
    st.append(Paragraph(
        'Ha egy csomag nem pontosan illik arra, amire szükséged van, szólj — '
        'a legtöbb projekt egyedi összeállítással indul.<br/>'
        '<b>info@soulsilvermarketing.com</b> · soulsilver.hu', S['body']))

    doc.build(st)
    print('OK ->', OUT)


if __name__ == '__main__':
    build()
