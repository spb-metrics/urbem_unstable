/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - SoluÃ§Ãµes em GestÃ£o PÃºblica                                *
    * @copyright (c) 2013 ConfederaÃ§Ã£o Nacional de MunicÃ­pos                         *
    * @author ConfederaÃ§Ã£o Nacional de MunicÃ­pios                                    *
    *                                                                                *
    * O URBEM CNM Ã© um software livre; vocÃª pode redistribuÃ­-lo e/ou modificÃ¡-lo sob *
    * os  termos  da LicenÃ§a PÃºblica Geral GNU conforme  publicada  pela FundaÃ§Ã£o do *
    * Software Livre (FSF - Free Software Foundation); na versÃ£o 2 da LicenÃ§a.       *
    *                                                                                *
    * Este  programa  Ã©  distribuÃ­do  na  expectativa  de  que  seja  Ãºtil,   porÃ©m, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implÃ­cita  de  COMERCIABILIDADE  OU *
    * ADEQUAÃÃO A UMA FINALIDADE ESPECÃFICA. Consulte a LicenÃ§a PÃºblica Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * VocÃª deve ter recebido uma cÃ³pia da LicenÃ§a PÃºblica Geral do GNU "LICENCA.txt" *
    * com  este  programa; se nÃ£o, escreva para  a  Free  Software Foundation  Inc., *
    * no endereÃ§o 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
/*
*
* Script de DDL e DML
*
* Versao 2.04.0
*
* Fabio Bertoldi - 20150415
*
*/

----------------
-- Ticket #22926
----------------

INSERT
  INTO administracao.configuracao
     ( cod_modulo
     , exercicio
     , parametro
     , valor
     )
     VALUES
     ( 5
     , '2015'
     , 'centro_custo'
     , 'false'
     );

ALTER TABLE sw_processo ADD COLUMN cod_centro INTEGER;
ALTER TABLE sw_processo ADD CONSTRAINT fk_processo_5 FOREIGN KEY                          (cod_centro)
                                                     REFERENCES almoxarifado.centro_custo (cod_centro);

