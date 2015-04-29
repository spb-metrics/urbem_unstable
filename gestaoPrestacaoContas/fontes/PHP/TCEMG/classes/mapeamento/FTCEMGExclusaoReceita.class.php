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
/**
    * Arquivo de mapeamento para a função que busca os dados da exclusao da receita
    * Data de Criação   : 27/01/2008

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Id: FTCEMGExclusaoReceita.class.php 61833 2015-03-09 12:59:57Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTCEMGExclusaoReceita extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function FTCEMGExclusaoReceita()
    {
        parent::Persistente();

        $this->setTabela('tcemg.fn_exclusao_receita');

        $this->AddCampo('exercicio'     ,'varchar',false,''    ,false,false);
        $this->AddCampo('cod_entidade'  ,'varchar',false,''    ,false,false);
        $this->AddCampo('mes'           ,'integer',false,''    ,false,false);
    }

    public function montaRecuperaTodos()
    {
        $stSql  = "
            SELECT mes
                 , REPLACE(ROUND(contr_serv,2)::TEXT                , '.', '') AS contr_serv
                 , REPLACE(ROUND(compens_reg_prev,2)::TEXT          , '.', '') AS compens_reg_prev
                 , REPLACE(ROUND(fundacoes_transf_corrente,2)::TEXT , '.', '') AS fundacoes_transf_corrente
                 , REPLACE(ROUND(autarquias_transf_corrente,2)::TEXT, '.', '') AS autarquias_transf_corrente
                 , REPLACE(ROUND(empestdep_transf_corrente,2)::TEXT , '.', '') AS empestdep_transf_corrente
                 , REPLACE(ROUND(demaisent_transf_corrente,2)::TEXT , '.', '') AS demaisent_transf_corrente
                 , REPLACE(ROUND(fundacoes_transf_capital,2)::TEXT  , '.', '') AS fundacoes_transf_capital
                 , REPLACE(ROUND(autarquias_transf_capital,2)::TEXT , '.', '') AS autarquias_transf_capital
                 , REPLACE(ROUND(empestdep_transf_capital,2)::TEXT  , '.', '') AS empestdep_transf_capital
                 , REPLACE(ROUND(demaisent_transf_capital,2)::TEXT  , '.', '') AS demaisent_transf_capital
                 , REPLACE(ROUND(out_duplic,2)::TEXT                , '.', '') AS out_duplic
                 , REPLACE(ROUND(contr_patronal,2)::TEXT            , '.', '') AS contr_patronal
                 , '' AS desc_outras_duplic
              FROM " . $this->getTabela() . "('" . $this->getDado('exercicio') . "','" . $this->getDado('cod_entidade') . "'," . $this->getDado('mes') . ") as tabela
                                               ( mes                        INTEGER,
                                                 contr_serv                 NUMERIC(14,2),
                                                 compens_reg_prev           NUMERIC(14,2),
                                                 fundacoes_transf_corrente   NUMERIC(14,2),
                                                 autarquias_transf_corrente NUMERIC(14,2),
                                                 empestdep_transf_corrente  NUMERIC(14,2),
                                                 demaisent_transf_corrente  NUMERIC(14,2),
                                                 fundacoes_transf_capital   NUMERIC(14,2),
                                                 autarquias_transf_capital  NUMERIC(14,2),
                                                 empestdep_transf_capital   NUMERIC(14,2),
                                                 demaisent_transf_capital   NUMERIC(14,2),
                                                 out_duplic                 NUMERIC(14,2),
                                                 contr_patronal             NUMERIC(14,2)
                                                )";

        return $stSql;
    }

}
