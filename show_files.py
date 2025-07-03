import os

def print_directory_structure(start_path, indent='  ', level=0):
    try:
        entries = sorted(os.listdir(start_path))
    except PermissionError:
        print(f"{indent * level}-- Permission Denied")
        return

    for entry in entries:
        if entry.startswith('.'):
            continue  # Skip hidden files and directories

        full_path = os.path.join(start_path, entry)
        is_dir = os.path.isdir(full_path)

        # Print the entry with indentation
        print(f"{indent * level}- {entry}/" if is_dir else f"{indent * level}- {entry}")

        if is_dir:
            print_directory_structure(full_path, indent, level + 1)

if __name__ == "__main__":
    # Get the directory where this script is located
    script_dir = os.path.dirname(os.path.abspath(__file__))
    
    print(f"Directory structure of '{script_dir}':")
    print_directory_structure(script_dir)