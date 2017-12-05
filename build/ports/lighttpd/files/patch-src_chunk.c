--- src/chunk.c.orig	2017-11-11 17:30:25.000000000 +0100
+++ src/chunk.c	2017-12-05 20:27:50.000000000 +0100
@@ -741,7 +741,7 @@
 	chunkqueue_remove_finished_chunks(cq);
 	if (chunkqueue_is_empty(cq)) return;
 
-	for (c = cq->first; c->next; c = c->next) {
+	for (c = cq->first; c && c->next; c = c->next) {
 		if (0 == chunk_remaining_length(c->next)) {
 			chunk *empty = c->next;
 			c->next = empty->next;
