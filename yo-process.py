import os
import pymorphy2

morph = pymorphy2.MorphAnalyzer()

def replace_e_with_yo(text):
    words = text.split()
    new_words = []
    for word in words:
        parsed_word = morph.parse(word)[0]
        if 'ё' in parsed_word.normal_form:
            new_word = word.replace('е', 'ё')
            new_words.append(new_word)
        else:
            new_words.append(word)
    return ' '.join(new_words)

def process_file(file_path):
    with open(file_path, 'r', encoding='utf-8') as file:
        content = file.read()
    new_content = replace_e_with_yo(content)
    with open(file_path, 'w', encoding='utf-8') as file:
        file.write(new_content)

def process_folder(folder_path):
    for root, _, files in os.walk(folder_path):
        for file in files:
            if file.endswith('.html') or file.endswith('.md'):
                file_path = os.path.join(root, file)
                process_file(file_path)

process_folder('/Users/ip/Projects/leonid-pavlov/yo-process')
