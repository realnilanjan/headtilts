import os

DEPLOYMENT_PATH = "/home1/headtzxc/public_html/"
OUTPUT_FILE = "deployment.yml"
SOURCE_DIR = "."

def is_not_dot_file(name):
    return not name.startswith(".")

def generate_copy_commands():
    commands = []
    for root, dirs, files in os.walk(SOURCE_DIR):
        # Skip hidden directories at all levels
        dirs[:] = [d for d in dirs if is_not_dot_file(d)]

        for file in files:
            if is_not_dot_file(file):
                full_path = os.path.join(root, file)
                relative_path = os.path.relpath(full_path, SOURCE_DIR)
                command = f"    - /bin/cp {relative_path} $DEPLOYPATH ({relative_path})"
                commands.append(command)
    return commands

def write_deployment_yml(commands):
    with open(OUTPUT_FILE, "w") as f:
        f.write("deployment:\n")
        f.write("  tasks:\n")
        f.write("    - export DEPLOYPATH=/home1/headtzxc/public_html/\n")
        for cmd in commands:
            f.write(cmd + "\n")

if __name__ == "__main__":
    print("Scanning files...")
    copy_commands = generate_copy_commands()
    write_deployment_yml(copy_commands)
    print(f"✅ Generated '{OUTPUT_FILE}' with {len(copy_commands)} copy tasks.")