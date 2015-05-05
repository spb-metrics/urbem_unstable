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
    * Arquivo de mapeamento para a função que busca os dados do recurso da alienacao do ativo
    * Data de Criação   : 29/01/2008

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTCEMGRecursoAlienacaoAtivo extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function FTCEMGRecursoAlienacaoAtivo()
    {
        parent::Persistente();

        $this->setTabela('tcemg.fn_recurso_alienacao_ativo');

        $this->AddCampo('exercicio'     ,'varchar',false,''    ,false,false);
        $this->AddCampo('cod_entidade'  ,'varchar',false,''    ,false,false);
        $this->AddCampo('mes'           ,'integer',false,''    ,false,false);
    }

    public function montaRecuperaTodos()
    {
        $stSql  = "
            SELECT mes
                 , ROUND(saldo_anterior,2) AS saldo_anterior
                 , ROUND(rec_realizada,2) AS rec_realizada
                 , ROUND(desp_emp,2) AS desp_emp
                 , ROUND(desp_liq,2) AS desp_liq
                 , ROUND(desp_paga,2) AS desp_paga
                 , LPAD(cod_vinc::VARCHAR,2,'0') AS cod_vinc
                 , ' ' AS cod_entidade
              FROM ".$this->getTabela()."('" . $this->getDado('exercicio') . "'," . $this->getDado('mes') . ",'" . $this->getDado('cod_entidade') . "') AS retorno
                                          (  mes                 INTEGER,
                                             saldo_anterior      NUMERIC(14,2),
                                             rec_realizada       NUMERIC(14,2),
                                             desp_emp            NUMERIC(14,2),
                                             desp_liq            NUMERIC(14,2),
                                             desp_paga           NUMERIC(14,2),
                                             cod_vinc            INTEGER,
                                             cod_entidade        INTEGER
                                          )";
        return $stSql;
    }

}
