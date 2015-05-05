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
  * Página de Mapeamento da tabela: tcemg.registro_precos_orgao
  * Data de Criação: 27/02/2015

  * @author Analista:      Gelson
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore

  $Id: TTCEMGRegistroPrecosOrgao.class.php 61913 2015-03-13 18:55:57Z franver $
  $Date: 2015-03-13 15:55:57 -0300 (Sex, 13 Mar 2015) $
  $Author: franver $
  $Rev: 61913 $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTCEMGRegistroPrecosOrgao extends Persistente {
    
    /**
    * Método Construtor
    * @access Public
    */
    public function TTCEMGRegistroPrecosOrgao()
    {
        parent::Persistente();
        $this->setTabela('tcemg.registro_precos_orgao');
        $this->setComplementoChave('cod_entidade, numero_registro_precos, exercicio_registro_precos, interno, numcgm_gerenciador, exercicio_unidade, num_unidade, num_orgao');
        
	    $this->AddCampo('cod_entidade'                , 'integer', true,  '',  true,  true);
        $this->AddCampo('numero_registro_precos'      , 'integer', true,  '',  true,  true);
	    $this->AddCampo('exercicio_registro_precos'   , 'varchar', true, '4',  true,  true);
        $this->AddCampo('interno'                     , 'boolean', true,  '',  true,  true);
        $this->AddCampo('numcgm_gerenciador'          , 'integer', true,  '',  true,  true);
        $this->AddCampo('exercicio_unidade'           , 'varchar', true, '4',  true,  true);
        $this->AddCampo('num_unidade'                 , 'integer', true,  '',  true,  true);
        $this->AddCampo('num_orgao'                   , 'integer', true,  '',  true,  true);
        $this->AddCampo('participante'                , 'boolean', true,  '', false, false);
        $this->AddCampo('numero_processo_adesao'      , 'integer',false,  '', false, false);
        $this->AddCampo('exercicio_adesao'            , 'varchar',false, '4', false, false);
        $this->AddCampo('dt_publicacao_aviso_intencao',    'date',false,  '', false, false);
        $this->AddCampo('dt_adesao'                   ,    'date',false,  '', false, false);
        $this->AddCampo('gerenciador'                 , 'boolean', true, '4', false, false);
    }

    public function __destruct(){}
    
}

?>