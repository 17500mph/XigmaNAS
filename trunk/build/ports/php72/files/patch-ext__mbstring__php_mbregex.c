--- ext/mbstring/php_mbregex.c.orig	2018-02-27 17:33:04.000000000 +0100
+++ ext/mbstring/php_mbregex.c	2018-03-23 21:32:43.000000000 +0100
@@ -45,7 +45,7 @@
 	HashTable ht_rc;
 	zval search_str;
 	zval *search_str_val;
-	unsigned int search_pos;
+	size_t search_pos;
 	php_mb_regex_t *search_re;
 	OnigRegion *search_regs;
 	OnigOptionType regex_default_options;
@@ -65,7 +65,6 @@
 {
 	pglobals->default_mbctype = ONIG_ENCODING_UTF8;
 	pglobals->current_mbctype = ONIG_ENCODING_UTF8;
-	zend_hash_init(&(pglobals->ht_rc), 0, NULL, php_mb_regex_free_cache, 1);
 	ZVAL_UNDEF(&pglobals->search_str);
 	pglobals->search_re = (php_mb_regex_t*)NULL;
 	pglobals->search_pos = 0;
@@ -79,7 +78,6 @@
 /* {{{ _php_mb_regex_globals_dtor */
 static void _php_mb_regex_globals_dtor(zend_mb_regex_globals *pglobals)
 {
-	zend_hash_destroy(&pglobals->ht_rc);
 }
 /* }}} */
 
@@ -126,7 +124,9 @@
 /* {{{ PHP_RINIT_FUNCTION(mb_regex) */
 PHP_RINIT_FUNCTION(mb_regex)
 {
-	return MBSTRG(mb_regex_globals) ? SUCCESS: FAILURE;
+	if (!MBSTRG(mb_regex_globals)) return FAILURE;
+	zend_hash_init(&MBREX(ht_rc), 0, NULL, php_mb_regex_free_cache, 0);
+	return SUCCESS;
 }
 /* }}} */
 
@@ -145,7 +145,7 @@
 		onig_region_free(MBREX(search_regs), 1);
 		MBREX(search_regs) = (OnigRegion *)NULL;
 	}
-	zend_hash_clean(&MBREX(ht_rc));
+	zend_hash_destroy(&MBREX(ht_rc));
 
 	return SUCCESS;
 }
@@ -183,7 +183,7 @@
 	OnigEncoding code;
 } php_mb_regex_enc_name_map_t;
 
-php_mb_regex_enc_name_map_t enc_name_map[] = {
+static const php_mb_regex_enc_name_map_t enc_name_map[] = {
 #ifdef ONIG_ENCODING_EUC_JP
 	{
 		"EUC-JP\0EUCJP\0X-EUC-JP\0UJIS\0EUCJP\0EUCJP-WIN\0",
@@ -366,7 +366,7 @@
 static OnigEncoding _php_mb_regex_name2mbctype(const char *pname)
 {
 	const char *p;
-	php_mb_regex_enc_name_map_t *mapping;
+	const php_mb_regex_enc_name_map_t *mapping;
 
 	if (pname == NULL || !*pname) {
 		return ONIG_ENCODING_UNDEF;
@@ -387,7 +387,7 @@
 /* {{{ php_mb_regex_mbctype2name */
 static const char *_php_mb_regex_mbctype2name(OnigEncoding mbctype)
 {
-	php_mb_regex_enc_name_map_t *mapping;
+	const php_mb_regex_enc_name_map_t *mapping;
 
 	for (mapping = enc_name_map; mapping->names != NULL; mapping++) {
 		if (mapping->code == mbctype) {
@@ -441,7 +441,7 @@
  * regex cache
  */
 /* {{{ php_mbregex_compile_pattern */
-static php_mb_regex_t *php_mbregex_compile_pattern(const char *pattern, int patlen, OnigOptionType options, OnigEncoding enc, OnigSyntaxType *syntax)
+static php_mb_regex_t *php_mbregex_compile_pattern(const char *pattern, size_t patlen, OnigOptionType options, OnigEncoding enc, OnigSyntaxType *syntax)
 {
 	int err_code = 0;
 	php_mb_regex_t *retval = NULL, *rc = NULL;
@@ -449,7 +449,7 @@
 	OnigUChar err_str[ONIG_MAX_ERROR_MESSAGE_LEN];
 
 	rc = zend_hash_str_find_ptr(&MBREX(ht_rc), (char *)pattern, patlen);
-	if (!rc || rc->options != options || rc->enc != enc || rc->syntax != syntax) {
+	if (!rc || onig_get_options(rc) != options || onig_get_encoding(rc) != enc || onig_get_syntax(rc) != syntax) {
 		if ((err_code = onig_new(&retval, (OnigUChar *)pattern, (OnigUChar *)(pattern + patlen), options, enc, syntax, &err_info)) != ONIG_NORMAL) {
 			onig_error_code_to_str(err_str, err_code, &err_info);
 			php_error_docref(NULL, E_WARNING, "mbregex compile err: %s", err_str);
@@ -576,11 +576,11 @@
 
 /* {{{ _php_mb_regex_init_options */
 static void
-_php_mb_regex_init_options(const char *parg, int narg, OnigOptionType *option, OnigSyntaxType **syntax, int *eval)
+_php_mb_regex_init_options(const char *parg, size_t narg, OnigOptionType *option, OnigSyntaxType **syntax, int *eval)
 {
-	int n;
+	size_t n;
 	char c;
-	int optm = 0;
+	OnigOptionType optm = 0;
 
 	*syntax = ONIG_SYNTAX_RUBY;
 
@@ -1096,7 +1096,8 @@
 	OnigUChar *pos, *chunk_pos;
 	size_t string_len;
 
-	int n, err;
+	int err;
+	size_t n;
 	zend_long count = -1;
 
 	if (zend_parse_parameters(ZEND_NUM_ARGS(), "ss|l", &arg_pattern, &arg_pattern_len, &string, &string_len, &count) == FAILURE) {
@@ -1118,16 +1119,16 @@
 	err = 0;
 	regs = onig_region_new();
 	/* churn through str, generating array entries as we go */
-	while (count != 0 && (pos - (OnigUChar *)string) < (ptrdiff_t)string_len) {
-		int beg, end;
+	while (count != 0 && (size_t)(pos - (OnigUChar *)string) < string_len) {
+		size_t beg, end;
 		err = onig_search(re, (OnigUChar *)string, (OnigUChar *)(string + string_len), pos, (OnigUChar *)(string + string_len), regs, 0);
 		if (err < 0) {
 			break;
 		}
 		beg = regs->beg[0], end = regs->end[0];
 		/* add it to the array */
-		if ((pos - (OnigUChar *)string) < end) {
-			if ((size_t)beg < string_len && beg >= (chunk_pos - (OnigUChar *)string)) {
+		if ((size_t)(pos - (OnigUChar *)string) < end) {
+			if (beg < string_len && beg >= (size_t)(chunk_pos - (OnigUChar *)string)) {
 				add_next_index_stringl(return_value, (char *)chunk_pos, ((OnigUChar *)(string + beg) - chunk_pos));
 				--count;
 			} else {
@@ -1217,7 +1218,8 @@
 {
 	char *arg_pattern = NULL, *arg_options = NULL;
 	size_t arg_pattern_len, arg_options_len;
-	int n, i, err, pos, len, beg, end;
+	int err;
+	size_t n, i, pos, len, beg, end;
 	OnigOptionType option;
 	OnigUChar *str;
 	OnigSyntaxType *syntax;
@@ -1341,7 +1343,7 @@
    Initialize string and regular expression for search. */
 PHP_FUNCTION(mb_ereg_search_init)
 {
-	size_t argc = ZEND_NUM_ARGS();
+	int argc = ZEND_NUM_ARGS();
 	zend_string *arg_str;
 	char *arg_pattern = NULL, *arg_options = NULL;
 	size_t arg_pattern_len = 0, arg_options_len = 0;
@@ -1401,7 +1403,7 @@
    Get matched substring of the last time */
 PHP_FUNCTION(mb_ereg_search_getregs)
 {
-	int n, i, len, beg, end;
+	size_t n, i, len, beg, end;
 	OnigUChar *str;
 
 	if (MBREX(search_regs) != NULL && Z_TYPE(MBREX(search_str)) == IS_STRING) {
@@ -1447,7 +1449,7 @@
 	if ((position < 0) && (!Z_ISUNDEF(MBREX(search_str))) && (Z_TYPE(MBREX(search_str)) == IS_STRING)) {
 		position += Z_STRLEN(MBREX(search_str));
 	}
-		
+
 	if (position < 0 || (!Z_ISUNDEF(MBREX(search_str)) && Z_TYPE(MBREX(search_str)) == IS_STRING && (size_t)position > Z_STRLEN(MBREX(search_str)))) {
 		php_error_docref(NULL, E_WARNING, "Position is out of range");
 		MBREX(search_pos) = 0;
