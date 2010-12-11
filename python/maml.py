# -*- coding: cp1251 -*-
import os, shutil, sys, re, glob, Image, time
from zipfile import ZipFile

#---------------OPTIONS-----------------
bgcolor = (110, 110, 110, 255) # #6e6e6e
txcolor = (255, 255, 255, 255) # text color
equation_max_size = 5000 #i.e. 200x100 or 20x1000
equation_max_height = 100
debug = False
#---------------OPTIONS-----------------

usage = """usage: %s dir

_dir_ should contain exactly one htm(l) file
containg the main text of the article

.* For documents converted from Mathematica
_dir_ MUST contain the directory "HTMLFiles"
even if it may be empty
.* For documents converted from Word/Mathtype
_dir_ may contain a directory with the same
name as the htm(l) file, where the sprites
are stored.""" % os.path.basename(sys.argv[0])

# some global vars
time_start = time.time()
images = {} #Image names
css = "" # CSS sprite data

# Main function
def maml(folder):
    time_start = time.time() #Working time
    
    if len(folder) < 2: #at least C:
        print("path \"%s\" too short" % folder)
        return
        
    if folder[-1] != '\\' or folder[-1] != '/':
        folder = folder + '\\'
        
    if not os.path.exists(folder):
        print("path \"%s\" does not exists" % folder)
        return

    # List htm(l) files in folder
    files = glob.glob(folder + '*.htm') + glob.glob(folder + '*.html')

    if len(files) == 0:
        print("No htm(l) files found in \"%s\"" % folder)
        return

    if len(files) > 1:
        print("Possible ambiguity: more than one htm(l) files found in \"" + folder + "\": " + dir_list(files))
        return

    html_file = files[0]
    out_file = os.path.basename(html_file).rpartition('.')[0] + '.maml';

    # All looks well, start working!
    # ------------------------------
    print("Starting Job\n---------------\n")
    
    # Create a temp dir
    temp_dir = folder + "QZYGX2R8\\"
    if not os.path.exists(temp_dir):
        os.mkdir(temp_dir)

    # Next is incompatible with PY 3.x
    print "Type: ",

    conv_type = "MTP" #Default is Word/Mathtype
    
    # Has it not been converted from Mathematica?
    if os.path.exists(folder + "HTMLFiles"):
        conv_type = "MTM" #Mathematica

    if (conv_type == "MTM"): #Mathematica
        print ("Mathematica")
        # Set location of sprites
        sprite_dir = folder + "HTMLFiles"
        tidy_function = tidy_mathematica

    if (conv_type == "MTP"): # Word/Mathtype
        print ("Word/Mathtype")
        # Set location of sprites
        sprite_dir = folder + os.path.basename(html_file).rpartition('.')[0] + "\\"
        tidy_function = tidy_mathtype
        
    # Process Sprites for any kind of HTML
    print("Processing sprites...")
    process_sprites(sprite_dir, temp_dir, conv_type)
    print("Sprites ready.")

    # Process HTML
    print "Processing " + os.path.basename(html_file).rpartition('.')[0] + "...",
    tidy_function(html_file, temp_dir)
    print("done.")

    # Zip contents to file
    print "Zipping files...",
    zip_filename = folder + out_file
    zip_file = ZipFile(zip_filename, 'w')

    work_files = glob.glob(temp_dir + 'mth_*.*')
    for z in work_files:
        zip_file.write(z, os.path.basename(z))

    zip_file.close()
    print("done.")
    
    if not debug:
        # Remove all temporary files
        print "Removing temp files...",
        for z in work_files:
            os.remove(z)

        # Remove temp_dir if empty
        try:
            os.rmdir(temp_dir)
        except OSError: pass
        print("done.")
    
    print("\nConversion completed in %s seconds." % round(time.time() - time_start, 2))
    
# Process Mathematica-typed HTMLs
def tidy_mathematica(html_in, html_out):
    return

# Process Word/Mathtype-typed HTMLs
def tidy_mathtype(html_in, dir_out):

    # Before exporting page, remove any text raising
    #
    # The rxport pages, using the following options:
    #  Webpage / Filtered
    #  
    
    # open file
    input1 = open(html_in)
    filename = os.path.basename(html_in).rpartition('.')[0]

    # load text
    text = input1.read()

    # close file
    input1.close()

    # do processing

    i = 1;
    print("WORD #" + str(i)); i+=1
    text = re.sub(r'(?is)\<html.*?\<body.*?\>', "", text)
    print("WORD #" + str(i)); i+=1
    text = re.sub(r'(?is)\<\/body.*$', "", text)

    print("WORD #" + str(i)); i+=1
    text = text.replace("=MsoNormal", '="MTPn"')
    print("WORD #" + str(i)); i+=1
    text = text.replace("=MsoFooter", '="MTPf"')

    # Remove commented text
    print("WORD #" + str(i)); i+=1
    text = re.sub(r'(?is)\<\!\-\-.*?\-\-\>', '', text)

    print("WORD #" + str(i)); i+=1
    text = re.sub(r'(?is)\<\![^\>]*?\>', '', text)

    print("WORD #" + str(i)); i+=1
    text = re.sub(r'\<div[^\>]*?\>', '', text)
    text = re.sub(r'\<\/div[^\>]*?\>', '', text)
    print("WORD #" + str(i)); i+=1
    text = text.replace("<![if !vml]>", "")
    print("WORD #" + str(i)); i+=1
    text = text.replace("<![if !supportFootnotes]>", "")
    print("WORD #" + str(i)); i+=1
    text = text.replace("<![endif]>", "")

    print("WORD #" + str(i)); i+=1
    text = re.sub(r'(?is)\<t([dh])[\s]*([^\>]*?)\>([\s]|\&nbsp\;)*\<p[\s]*[^\>]*?\>(([\s]|\&nbsp\;)*\<span[\s]*[^\>]*?\>([\s]|\&nbsp\;)*\<\/span\>)*\<\/p\>', r'<t\1 \2>&nbsp;', text)
    text = re.sub(r'(?is)\<p[\s]*[^\>]*?\>(([\s]|\&nbsp\;)*\<span[\s]*[^\>]*?\>([\s]|\&nbsp\;)*\<\/span\>)*\<\/p\>', '', text)
    text = re.sub(r'(?is)\<p[\s]*[^\>]*?\>([\s]|\&nbsp\;)*\<\/p\>', '', text)
#    print("WORD #" + str(i)); i+=1
#    text = re.sub(r'(?is)\<\!\[if \!supportFootnotes\]\>.*?\<\!\[endif]\>', '', text)

    print("WORD #" + str(i)); i+=1
    text = text.replace("<o:p>", "")
    print("WORD #" + str(i)); i+=1
    text = text.replace("</o:p>", "")

    print("WORD #" + str(i)); i+=1
    text = text.replace('bgcolor=white', "")
    text = text.replace(chr(183), '<!img src="./layout/gfx/bullet.gif">')

    text = re.sub(r'\<([\/]{0,1})h[0-9]([\>]*?)\>', r'<\1p \2>', text)

    # make CSS sprites
    print("#CSS1")
    text = re.sub(r'(?is)\<span([^\>]*?)style[\s]*\=[\s]*(\')([^\']*?)\'([^\>]*?)\>[\s]*<img[\s]*([^\>]*?)height[\s]*\=[\s]*([0-9]+)[^\>]+src\=\"(.*?)\".*?\>', img2css2, text)

    print("#CSS2")
    text = re.sub(r'(?is)\<img[^\>]*?src\=\"(.*?)\".*?\>', img2css, text)

    text = text.replace('<!img', '<img')

    #remove width/height of TD/TH/TR
#    text = re.sub(r'(?is)(\<t[dhr][\s]+[^\>]*?)(width[\s]*?\=[\s]*?[\"\']{0,1}[0-9]+)([\"\']{0,1}[^\>]*?\>)', r'\1\3', text)
#    text = re.sub(r'(?is)(\<t[dhr][\s]+[^\>]*?)(height[\s]*?\=[\s]*?[\"\']{0,1}[0-9]+)([\"\']{0,1}[^\>]*?\>)', r'\1\3', text)
    
    bad_styles = [
        'border-collapse',
        'layout-grid-mode',
        'tab-stops',
        'z-index',
        'position',
        'left',
        'right',
        'top',
        'bottom',
#        'width',
#        'height',
        'color',
        'font',
        'font-family',
        'font-size'
        ]
    def fix_style(self):

        # Groups:
        #
        # <span class="MTPn" align=center style= ' margin-right:3.1pt; text-align:center;mso-pagination:none
        #  |                             |       | |                                                       |
        #  \_____________ 1 _____________/       2 \_________________________ 3 ___________________________/

        styles_all = self.group(3).replace("\n", "").split(';')
        styles_ok = []

        for s in styles_all:
            current_style = s.strip().rpartition(':')
            s0 = current_style[0].strip()
            s2 = current_style[2].strip()
            s2 = s2.replace("white", "transparent")
            
            if not s0.startswith('mso-') and s0 not in bad_styles:
                styles_ok.append(s0 + ":" + s2)

        return "<" + self.group(1) + "style=" + self.group(2) + ";".join(styles_ok)

    print("WORD #" + str(i)); i+=1
    text = re.sub(r'(?is)\<([^\>]*?)style[\s]*\=[\s]*([\"\'])(.*?)(?=\2)', fix_style, text)
#    text = re.sub(r'(?is)\<(.*?)style[\s]*\=(.*?)mso-.*?\:.*?;', r'<\1style=\2', text)
#    print("WORD #" + str(i)); i+=1
#    text = re.sub(r'(?is)\<(.*?)style[\s]*\=(.*?)mso-.*?\:.*?\'', r"<\1style=\2'", text)

    print("#final")
    text = "<style>\n" + css + "</style>\n" + text

    if debug:
        text = '<head><meta http-equiv=Content-Type content="text/html; charset=windows-1251"><meta name=Generator content="Microsoft Word 11 (filtered)"><style>html {font-size: 16px; background-color: rgb(110, 110, 110); color: #FFFFFF;}</style>' + text

    
    # save file
    if debug:
        output1 = open(dir_out + "mth_text.html", 'w')
    else:
        output1 = open(dir_out + "mth_text.php", 'w')
    output1.write(text)
    output1.close()
    return

# Process sprites
def process_sprites(dir_in, dir_out, conv_type):

    process_sprites.EQUATION = "eqs"
    process_sprites.EQUATION_LARGE = "eqs_lg"
    process_sprites.FULLCOLOR = "fullcolor"

    # Checked paths
    if not os.path.exists(dir_in) or not os.path.exists(dir_out):
        print("Directory \"" + dir_out + "\" not found. No sprites processed.")
        return

    # list GIF and PNG images (only these may contain equations)
    # JPG and BMP will NOT be processed as they are true color
    img_names = glob.glob(dir_in + "*.gif") + glob.glob(dir_in + "*.png") + glob.glob(dir_in + "*.bmp")

    global images, css
    images = {} # Warning! images_names is declared globally
    css = "" # Warning! css is declared globally

    images_eqs = [] #Image objects
    images_eqs_lg = []
    images_fullcolor = []
    for x in img_names:
        i = Image.open(x)
        res = image_type(i)
        images[os.path.basename(x).rpartition('.')[0]] = res
        if res == image_type.EQUATION:
            images_eqs.append((i, os.path.basename(x)))
        if res == image_type.EQUATION_LARGE:
            images_eqs_lg.append((i, os.path.basename(x)))
        if res == image_type.FULLCOLOR:
            images_fullcolor.append((i, os.path.basename(x)))

    # import the JPEGs, too (they are surely full color)
    img_names = glob.glob(dir_in + "*.jpg") + glob.glob(dir_in + "*.jpeg")
    for x in img_names:
        images[os.path.basename(x).rpartition('.')[0]] = image_type.FULLCOLOR
        images_fullcolor.append((Image.open(x), os.path.basename(x)))

    print "Processing equations...",
    if len(images_eqs) > 0:
        print(str(len(images_eqs)) + " found")
        css += css_bg("spr_" + process_sprites.EQUATION, "mth_spr1.png")
        sprite_eqs = process_images(images_eqs, conv_type)
        sprite_eqs.save(dir_out + "mth_spr1.png")
        del images_eqs
        del sprite_eqs
    else:
        print("none found")

    print "Processing equations (large)...",
    if len(images_eqs_lg) > 0:
        print(str(len(images_eqs_lg)) + " found")
        css += css_bg("spr_" + process_sprites.EQUATION_LARGE, "mth_spr2.png")
        sprite_eqs_lg = process_images(images_eqs_lg, conv_type)
        sprite_eqs_lg.save(dir_out + "mth_spr2.png")
        del images_eqs_lg
        del sprite_eqs_lg
    else:
        print("none found")
        
    print "Processing fullcolor...",
    if len(images_fullcolor) > 0:
        print(str(len(images_fullcolor)) + " found")
        css += css_bg("spr_" + process_sprites.FULLCOLOR, "mth_spr3.png")
        sprite_fullcolor = process_images(images_fullcolor)
        sprite_fullcolor.save(dir_out + "mth_spr3.png")
        del images_fullcolor
        del sprite_fullcolor
    else:
        print("none found")


def process_images(images, conv_type = None):
    
    width_max = 0
    height_max = 0
    width_all = 0
    height_all = 0

    for i in images:
        # images: [(Image, basename), ...]
        
        if (i[0].size[0] > width_max):
            width_max = i[0].size[0]
        if (i[0].size[1] > height_max):
            height_max = i[0].size[1]
        width_all += i[0].size[0]
        height_all += i[0].size[1]

    global css
    
    if (width_all * height_max < width_max * height_all):
        
        # horizontal layout
        canvas = Image.new('RGBA', (width_all, height_max), bgcolor)
        
        x = 0
        for i in images:
            canvas.paste(i[0], (x,0))
            css += 'img#spr-' + i[1].rpartition('.')[0] + ' {background-position: ' + str(-x) + 'px 0px; width: ' + str(i[0].size[0]) + '; height: ' + str(i[0].size[1]) + ';}\n'
            x += i[0].size[0]
    else:
        
        # vertical layout
        canvas = Image.new('RGBA', (width_max, height_all), (255,255,255,0))
        
        x = 0
        for i in images:
            canvas.paste(i[0], (0,x))
            css += 'img#spr-' + i[1].rpartition('.')[0] + ' {background-position: 0px ' + str(-x) + 'px; width: ' + str(i[0].size[0]) + '; height: ' + str(i[0].size[1]) + ';}\n'
            x += i[0].size[1]

    if conv_type != None:
       canvas = fix_colors(canvas, conv_type)

    return canvas

def css_bg(style_name, file_name):
    if not debug:
        return "img." + style_name + " {background-image: url(<?PAGE_PATH?>" + file_name + "); background-repeat: no-repeat; vertical-align:middle; border-top: 0.5ex solid rgb(" + str(bgcolor[0]) + "," + str(bgcolor[1]) + "," + str(bgcolor[2]) + ")}\n"
    else:
        return "img." + style_name + " {background-image: url(" + file_name + "); background-repeat: no-repeat; vertical-align:middle; border-top: 0.5ex solid rgb(" + str(bgcolor[0]) + "," + str(bgcolor[1]) + "," + str(bgcolor[2]) + ")}\n"

# Process dir lists
def dir_list(self):
    res = ""
    for k in range(0, len(self)):
        res += "\n\"" + self[k] + "\""
    return res

# Filter non-two color images, i.e. NOT equations, but plots, photos, etc...
def image_type(self):
    # self is image file

    image_type.EQUATION = 0
    image_type.EQUATION_LARGE = 1
    image_type.FULLCOLOR = 3

    if len(set(self.getdata())) > 2: # check number of colors
        return 3 # full-color image, i.e. not an equation
    if self.size[1] > equation_max_height: # check height size
        return 1 # equation of larger size, i.e. not very portable
    if self.size[0] * self.size[1] > equation_max_size: # check area size
        return 1 # equation of larger size, i.e. not very portable

    return 0 # OK

# Process img tags
def img2css(self):

    filename = os.path.basename(self.group(1)).rpartition('.')[0]
    text = '<img src="_.gif" id="spr-' + filename + '" class="spr_'
    try:
        if images[filename] == image_type.EQUATION:
            text += process_sprites.EQUATION
        if images[filename] == image_type.EQUATION_LARGE:
            text += process_sprites.EQUATION_LARGE
        if images[filename] == image_type.FULLCOLOR:
            text += process_sprites.FULLCOLOR
    except KeyError:
        text = 'undef'
    
    text += '">'
    return text

def img2css2(self):

    # Groups:
    #
    # <span align="right" style = ' position:relative; top:-6.0pt;' align="right">   <img   width = 130 height = 190 src="lx01/img012.png">
    # |    \_____ 1 _____/        | \_____________ 3 ____________/ \_____ 4 ____/            \___ 5 ___/         \6/      \______7______/ |
    # |                           2                                                                                                       |
    #  \___________________________________________________ 0 ____________________________________________________________________________/

    styles_all = self.group(3).replace("\n", "").split(';')
    styles_ok = []
    margin = ['0px', '0px', '0px', '0px']
    
    for s in styles_all:
        current_style = s.strip().rpartition(':')
        s0 = current_style[0].strip()
        s2 = current_style[2].strip()

        height = int(self.group(6))
        t = 0
        l = 0

        if s0 == "position":
            pass
        elif s0 == "top":
            t = css_size(s2)
        elif s0 == "bottom":
            t = -css_size(s2)
        elif s0 == "left":
            l = css_size(s2)
        elif s0 == "right":
            l = -css_size(s2)
        else:
            styles_ok.append(s0 + ":" + s2)

        if t == 0:
            margin[0] = '0.5ex'
        else:
            t = int(round(t - (height/2)))
            if t > 0:
                margin[0] = str(t) + 'px'
            else:
                margin[2] = str(abs(t)) + 'px'

        if l > 0:
            margin[1] = str(l) + 'px'
        if l < 0:
            margin[3] = str(abs(l)) + 'px'

    if margin[2] != '0':
        margin[0] = '0'
    
    filename = os.path.basename(self.group(7)).rpartition('.')[0]
    text = '<span' + self.group(1) + 'style=' + self.group(2) + ";".join(styles_ok) + self.group(2) + self.group(4) + '><!img ' + self.group(5) + 'height=' + self.group(6) + ' src="_.gif" style="margin:' + " ".join(margin) + '" id="spr-' + filename + '" class="spr_'
    try:
        if images[filename] == image_type.EQUATION:
            text += process_sprites.EQUATION
        if images[filename] == image_type.EQUATION_LARGE:
            text += process_sprites.EQUATION_LARGE
        if images[filename] == image_type.FULLCOLOR:
            text += process_sprites.FULLCOLOR
    except KeyError:
        text += 'undef'

    text += '">'
    
    return text

def css_size(self):
    reg = re.findall(r'^([+-]{0,1}([0-9]+)(\.[0-9]*){0,1})[\s]*(pt|px|\%|pc|in|cm|mm|ex){0,1}', self)
    if len(reg) == 0:
        return 0
    
    num = float(reg[0][0])
    typ = reg[0][3]

    if typ == "px":
        return num
    
    if typ == "pt":
        return num * 1.3

    return 0

# Process canvas
def fix_colors(self, conv_type): #self is an image
    # apply bgcolor instead of transparency
    if self.mode != 'RGBA':
        self = self.convert('RGBA')
    xcanvas = self.load()
    canvas_gif = Image.new("P", self.size)
    xcanvas_gif = canvas_gif.load()

    if conv_type == "MTP":
        for x in range(0, self.size[0]):
            for y in range(0, self.size[1]):
                if xcanvas[x, y] == (0,0,0,255):
                    xcanvas_gif[x,y] = 1 # index 1

    if conv_type == "MTM":
        for x in range(0, self.size[0]):
            for y in range(0, self.size[1]):
                if xcanvas[x,y] == txcolor:
                    xcanvas_gif[x,y] = 1 # index 1

    del xcanvas
    del xcanvas_gif

    canvas_gif.putpalette([
        bgcolor[0], bgcolor[1], bgcolor[2],
        txcolor[0], txcolor[1], txcolor[2]
    ])
    
    return canvas_gif

# Handle commandline input
if __name__ == "__main__":
    if len(sys.argv) < 1:
        print(usage)
    else:
        maml(sys.argv[1:])
#maml("R:\\w3")
