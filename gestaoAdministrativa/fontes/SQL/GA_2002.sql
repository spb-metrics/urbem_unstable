/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
/*
* Script de DDL e DML
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id:$
*
* Versão 2.00.2
*/

----------------
-- Ticket #18098
----------------

DROP VIEW economico.vw_responsavel_tecnico;

ALTER TABLE sw_cgm alter column complemento TYPE VARCHAR(30);
ALTER TABLE sw_cga alter column complemento TYPE VARCHAR(30);
ALTER TABLE sw_cgm alter column complemento_corresp TYPE VARCHAR(30);
ALTER TABLE sw_cga alter column complemento_corresp TYPE VARCHAR(30);

CREATE VIEW economico.vw_responsavel_tecnico AS
    SELECT rp.num_registro
         , rp.sequencia
         , cgm.numcgm
         , cgm.cod_municipio
         , cgm.cod_uf AS cod_uf_cgm
         , cgm.cod_municipio_corresp
         , cgm.cod_uf_corresp
         , cgm.cod_responsavel
         , cgm.nom_cgm
         , cgm.tipo_logradouro
         , cgm.logradouro
         , cgm.numero
         , cgm.complemento
         , cgm.bairro
         , cgm.cep
         , cgm.tipo_logradouro_corresp
         , cgm.logradouro_corresp
         , cgm.numero_corresp
         , cgm.complemento_corresp
         , cgm.bairro_corresp
         , cgm.cep_corresp
         , cgm.fone_residencial
         , cgm.ramal_residencial
         , cgm.fone_comercial
         , cgm.ramal_comercial
         , cgm.fone_celular
         , cgm.e_mail
         , cgm.e_mail_adcional
         , cgm.dt_cadastro
         , uf.cod_uf
         , uf.cod_pais
         , uf.nom_uf
         , uf.sigla_uf
         , pr.cod_profissao
         , pr.nom_profissao
         , pr.cod_conselho
         , pr.nom_conselho
         , pr.nom_registro
      FROM economico.responsavel_tecnico rp
      JOIN (
             SELECT p.cod_profissao
                  , p.nom_profissao
                  , c.cod_conselho
                  , c.nom_conselho
                  , c.nom_registro
               FROM cse.profissao p
               JOIN cse.conselho c
                 ON p.cod_conselho = c.cod_conselho
           ) pr
        ON pr.cod_profissao = rp.cod_profissao
      JOIN sw_cgm cgm
        ON rp.numcgm = cgm.numcgm
      JOIN sw_uf uf
        ON rp.cod_uf = uf.cod_uf
         ;

