import os, sys, re
usage = "usage: %s infile outfile" %         os.path.basename(sys.argv[0])
"""
if __name__ == "__main__":
    if len(sys.argv) < 1:
        print(usage)
    else:
        fixdoc(sys.argv[1])
"""
def fixdoc(filename):

    # open file
    input1 = open(filename + ".htm")

    # load text
    text = input1.read()

    # close file
    input1.close()

    # do processing

    # remove VML
    text = re.sub(r'\<html.*?\<p', "<p", text, 0, re.IGNORECASE | re.DOTALL)
    text = re.sub(r'\<\/div.*$', "", text, 0, re.IGNORECASE | re.DOTALL)
    text = re.sub(r'\<\!\-\-((.)*?)\-\-\>', '', text, 0, re.IGNORECASE | re.DOTALL)
    text = text.replace("<![if !vml]>", "")
    text = text.replace("<![endif]>", "")
    text = re.sub(r'mso\-text\-raise\:(.*?)(?=[\;\'])', "", text, 0, re.IGNORECASE | re.DOTALL)
    text = re.sub(r'\<span\sstyle\=\'mso\-spacerun\:yes\'\>.*?\<\/span\>', "&nbsp;", text, 0, re.IGNORECASE | re.DOTALL)
    text = text.replace("=MsoNormal", '="MathNormal"')
    text = text.replace("=MsoFooter", '="MathFooter"')
    

    # make CSS sprites
    text = re.sub(r'\<img .*?src\=\"' + filename + '\/(.*?)\.gif\".*?\>', r'<img class="mathtype" id="spr-\1" src="_blank.gif">', text, 0, re.IGNORECASE | re.DOTALL)
    
    # save file
    output1 = open(filename + "_out.htm", 'w')
    output1.write(text)
    output1.close()

fixdoc("x001")
