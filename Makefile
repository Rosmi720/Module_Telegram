MODULE_NAME := telegram_3b6df
ZIP_NAME := $(MODULE_NAME).zip
DIST_DIR := ../xc_vm/modules_archives

all: zip

zip:
	@mkdir -p $(DIST_DIR)
	@rm -f $(DIST_DIR)/$(ZIP_NAME)
	@zip -r $(abspath $(DIST_DIR))/$(ZIP_NAME) . -x ".git/*" "Makefile" "README.md"
	@cp -f $(abspath $(DIST_DIR))/$(ZIP_NAME) $(abspath $(DIST_DIR))/telegram_1.0.0.zip 2>/dev/null || true
	@echo "✓ Packaged to $(abspath $(DIST_DIR))/$(ZIP_NAME)"
