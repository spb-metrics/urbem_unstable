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
    * Arquivo de mapeamento para a função que busca os dados da despesa capital
    * Data de Criação   : 29/01/2008

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Id: FTCEMGDespesaCapital.class.php 62056 2015-03-27 14:53:30Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTCEMGDespesaCapital extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function FTCEMGDespesaCapital()
    {
        parent::Persistente();

        $this->setTabela('tcemg.fn_despesa_capital');

        $this->AddCampo('exercicio'     ,'varchar',false,''    ,false,false);
        $this->AddCampo('cod_entidade'  ,'varchar',false,''    ,false,false);
        $this->AddCampo('mes'           ,'integer',false,''    ,false,false);
    }

    public function montaRecuperaTodos()
    {
        $stSql  = "
            SELECT mes
                 , REPLACE( ROUND(desp_invest       ,2)::TEXT, '.', '') AS desp_invest
                 , REPLACE( ROUND(desp_inv_finan    ,2)::TEXT, '.', '') AS desp_inv_finan
                 , REPLACE( ROUND(desp_amort_div_int,2)::TEXT, '.', '') AS desp_amort_div_int
                 , REPLACE( ROUND(desp_amort_div_ext,2)::TEXT, '.', '') AS desp_amort_div_ext
                 , REPLACE( ROUND(desp_amort_div_mob,2)::TEXT, '.', '') AS desp_amort_div_mob
                 , REPLACE( ROUND(desp_out_desp_cap ,2)::TEXT, '.', '') AS desp_out_desp_cap
                 , REPLACE( ROUND(conc_emprestimos  ,2)::TEXT, '.', '') AS conc_emprestimos
                 , REPLACE( ROUND(aquisicao_titulos ,2)::TEXT, '.', '') AS aquisicao_titulos
                 , REPLACE( ROUND(incent_contrib    ,2)::TEXT, '.', '') AS incent_contrib
                 , REPLACE( ROUND(incent_inst_finan ,2)::TEXT, '.', '') AS incent_inst_finan
                 , LPAD(cod_tipo::varchar,2,'0') AS cod_tipo 
              FROM ".$this->getTabela()."('" . $this->getDado('exercicio') . "','" . $this->getDado('cod_entidade') . "'," . $this->getDado('mes') . ") AS retorno
                                          (  mes                 INTEGER,
                                             desp_invest         NUMERIC,
                                             desp_inv_finan      NUMERIC,
                                             desp_amort_div_int  NUMERIC,
                                             desp_amort_div_ext  NUMERIC,
                                             desp_amort_div_mob  NUMERIC,
                                             desp_out_desp_cap   NUMERIC,
                                             conc_emprestimos    NUMERIC,
                                             aquisicao_titulos   NUMERIC,
                                             incent_contrib      NUMERIC,
                                             incent_inst_finan   NUMERIC,
                                             cod_tipo            INTEGER
                                          )
          ORDER BY cod_tipo";

        return $stSql;
    }

}
