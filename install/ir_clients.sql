--
-- PostgreSQL database dump
--

-- Dumped from database version 9.6.18
-- Dumped by pg_dump version 9.6.18

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: specials; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA specials;


ALTER SCHEMA specials OWNER TO postgres;

--
-- Name: suppressions; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA suppressions;


ALTER SCHEMA suppressions OWNER TO postgres;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: blacklists; Type: TABLE; Schema: specials; Owner: postgres
--

CREATE TABLE specials.blacklists (
    id integer NOT NULL,
    email_md5 text NOT NULL
);


ALTER TABLE specials.blacklists OWNER TO postgres;

--
-- Name: seq_id_blacklists; Type: SEQUENCE; Schema: specials; Owner: postgres
--

CREATE SEQUENCE specials.seq_id_blacklists
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE specials.seq_id_blacklists OWNER TO postgres;

--
-- Name: seq_id_sup_list_2; Type: SEQUENCE; Schema: suppressions; Owner: h1
--

CREATE SEQUENCE suppressions.seq_id_sup_list_2
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE suppressions.seq_id_sup_list_2 OWNER TO h1;

--
-- Name: sup_list_2; Type: TABLE; Schema: suppressions; Owner: h1
--

CREATE TABLE suppressions.sup_list_2 (
    id integer NOT NULL,
    list_id integer NOT NULL,
    email_md5 text NOT NULL
);


ALTER TABLE suppressions.sup_list_2 OWNER TO h1;

--
-- Data for Name: blacklists; Type: TABLE DATA; Schema: specials; Owner: postgres
--

COPY specials.blacklists (id, email_md5) FROM stdin;
\.


--
-- Name: seq_id_blacklists; Type: SEQUENCE SET; Schema: specials; Owner: postgres
--

SELECT pg_catalog.setval('specials.seq_id_blacklists', 1, false);


--
-- Name: seq_id_sup_list_2; Type: SEQUENCE SET; Schema: suppressions; Owner: h1
--

SELECT pg_catalog.setval('suppressions.seq_id_sup_list_2', 1, false);


--
-- Data for Name: sup_list_2; Type: TABLE DATA; Schema: suppressions; Owner: h1
--

COPY suppressions.sup_list_2 (id, list_id, email_md5) FROM stdin;
\.


--
-- Name: blacklists blacklists_email_md5_key; Type: CONSTRAINT; Schema: specials; Owner: postgres
--

ALTER TABLE ONLY specials.blacklists
    ADD CONSTRAINT blacklists_email_md5_key UNIQUE (email_md5);


--
-- Name: blacklists c_pk_id_blacklists; Type: CONSTRAINT; Schema: specials; Owner: postgres
--

ALTER TABLE ONLY specials.blacklists
    ADD CONSTRAINT c_pk_id_blacklists PRIMARY KEY (id);


--
-- Name: sup_list_2 c_pk_id_sup_list_2; Type: CONSTRAINT; Schema: suppressions; Owner: h1
--

ALTER TABLE ONLY suppressions.sup_list_2
    ADD CONSTRAINT c_pk_id_sup_list_2 PRIMARY KEY (id);


--
-- Name: specials_blacklists_idx; Type: INDEX; Schema: specials; Owner: postgres
--

CREATE INDEX specials_blacklists_idx ON specials.blacklists USING btree (id, email_md5);

ALTER TABLE specials.blacklists CLUSTER ON specials_blacklists_idx;


--
-- Name: sup_list_2_idx; Type: INDEX; Schema: suppressions; Owner: h1
--

CREATE INDEX sup_list_2_idx ON suppressions.sup_list_2 USING btree (id, list_id, email_md5);


--
-- PostgreSQL database dump complete
--

