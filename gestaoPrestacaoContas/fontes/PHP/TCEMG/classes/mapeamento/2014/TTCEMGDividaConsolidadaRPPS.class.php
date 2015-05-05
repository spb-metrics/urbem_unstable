<?php
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
?>
<?php

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGDividaConsolidadaRPPS extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGDividaConsolidadaRPPS()
    {
        parent::Persistente();
    }

    public function montaRecuperaTodos()
    {
        $stSql = " SELECT 
                        ".$this->getDado('mes')." as mes
                        , CAST(COALESCE(div_contratual_demais           ,0.00) AS NUMERIC(14,2)) as div_contratual_demais
                        , CAST(COALESCE(div_contratual_ppp              ,0.00) AS NUMERIC(14,2)) as div_contratual_ppp
                        , CAST(COALESCE(div_mobiliaria                  ,0.00) AS NUMERIC(14,2)) as div_mobiliaria
                        , CAST(COALESCE(op_credito_inf_12               ,0.00) AS NUMERIC(14,2)) as op_credito_inf_12
                        , CAST(COALESCE(outras                          ,0.00) AS NUMERIC(14,2)) as outras
                        , CAST(COALESCE(parc_contr_sociais_prev         ,0.00) AS NUMERIC(14,2)) as parc_contr_sociais_prev
                        , CAST(COALESCE(parc_contr_sociais_demais       ,0.00) AS NUMERIC(14,2)) as parc_contr_sociais_demais
                        , CAST(COALESCE(parc_tributos                   ,0.00) AS NUMERIC(14,2)) as parc_tributos
                        , CAST(COALESCE(parc_fgts                       ,0.00) AS NUMERIC(14,2)) as parc_fgts
                        , CAST(COALESCE(precatorios_post                ,0.00) AS NUMERIC(14,2)) as precatorios_post
                        , CAST(COALESCE(div_contratual_demais_rpps      ,0.00) AS NUMERIC(14,2)) as div_contratual_demais_rpps
                        , CAST(COALESCE(div_contratual_ppp_rpps         ,0.00) AS NUMERIC(14,2)) as div_contratual_ppp_rpps
                        , CAST(COALESCE(div_mobiliaria_rpps             ,0.00) AS NUMERIC(14,2)) as div_mobiliaria_rpps
                        , CAST(COALESCE(op_credito_inf_12_rpps          ,0.00) AS NUMERIC(14,2)) as op_credito_inf_12_rpps
                        , CAST(COALESCE(outras_rpps                     ,0.00) AS NUMERIC(14,2)) as outras_rpps
                        , CAST(COALESCE(parc_contr_sociais_prev_rpps    ,0.00) AS NUMERIC(14,2)) as parc_contr_sociais_prev_rpps
                        , CAST(COALESCE(parc_contr_sociais_demais_rpps  ,0.00) AS NUMERIC(14,2)) as parc_contr_sociais_demais_rpps
                        , CAST(COALESCE(parc_tributos_rpps              ,0.00) AS NUMERIC(14,2)) as parc_tributos_rpps
                        , CAST(COALESCE(parc_fgts_rpps                  ,0.00) AS NUMERIC(14,2)) as parc_fgts_rpps
                        , CAST(COALESCE(precatorios_post_rpps           ,0.00) AS NUMERIC(14,2)) as precatorios_post_rpps
                    FROM tcemg.arquivo_divida_consolidada_rpps('".$this->getDado('exercicio')."'
                                                                ,'Mes'
                                                                ,".$this->getDado('mes')."
                                                                ,'".$this->getDado('cod_entidade')."'
                                                                ,'".$this->getDado('cod_entidade_rpps')."') 
                    AS tbl(
                             div_contratual_demais              NUMERIC
                            , div_contratual_ppp                NUMERIC
                            , div_mobiliaria                    NUMERIC
                            , op_credito_inf_12                 NUMERIC
                            , outras                            NUMERIC
                            , parc_contr_sociais_prev           NUMERIC
                            , parc_contr_sociais_demais         NUMERIC
                            , parc_tributos                     NUMERIC
                            , parc_fgts                         NUMERIC
                            , precatorios_post                  NUMERIC
                            , div_contratual_demais_rpps        NUMERIC
                            , div_contratual_ppp_rpps           NUMERIC
                            , div_mobiliaria_rpps               NUMERIC
                            , op_credito_inf_12_rpps            NUMERIC
                            , outras_rpps                       NUMERIC
                            , parc_contr_sociais_prev_rpps      NUMERIC
                            , parc_contr_sociais_demais_rpps    NUMERIC
                            , parc_tributos_rpps                NUMERIC
                            , parc_fgts_rpps                    NUMERIC
                            , precatorios_post_rpps             NUMERIC
                    )
        ";
        
        return $stSql;
    }

}
